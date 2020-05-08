<?php
declare(strict_types=1);

namespace A7digital\A7picsuggest\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020, a7digital GmbH
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use A7digital\A7picsuggest\Domain\Suggestion;
use Doctrine\DBAL\Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SuggestionAjaxController
{
    /**
     * @var ConnectionPool
     */
    private $connectionPool;

    /**
     * @var FileRepository
     */
    private $fileRepository;

    public function __construct()
    {
        $this->connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->fileRepository = GeneralUtility::makeInstance(FileRepository::class);
    }

    public function giveAllAvailableTags(ServerRequestInterface $request): ResponseInterface
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category');
        $queryBuilder->from('sys_category');
        $queryBuilder->addSelect('uid', 'title');
        return new JsonResponse($queryBuilder->execute()->fetchAll());
    }

    public function suggest(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $arguments = $request->getParsedBody();
            $uids = [];
            $tagWeights = [];
            foreach (json_decode($arguments['tags'], true) as $tag) {
                $uids[] = (int)$tag['uid'];
                $tagWeights[(int)$tag['uid']] = $tag['weight'];
            }
            $subQueryBuilder = $this->connectionPool->getQueryBuilderForTable('sys_category_record_mm');
            $subQueryBuilder->from('sys_category_record_mm', 'scrm');
            $subQueryBuilder->select('scrm.uid_foreign AS uid', 'scrm.weight AS weight', 'scrm.uid_local AS tagUid');
            $subQueryBuilder->andWhere('scrm.fieldname = "categories"', 'scrm.tablenames = "sys_file_metadata"', 'scrm.uid_local IN (:uids)');
            $subQueryBuilder->setParameter('uids', $uids, Connection::PARAM_INT_ARRAY);
            $suggestions = [];
            foreach ($subQueryBuilder->execute()->fetchAll() as $row) {
                $uid = $row['uid'];
                $tagUid = $row['tagUid'];
                $suggestion = $suggestions[$uid] ?? new Suggestion(0, $uid);
                $suggestion->setWeight($suggestion->getWeight() + ($row['weight'] ?? 1) * $tagWeights[$tagUid]);
                $suggestions[$uid] = $suggestion;
            }
            usort($suggestions, function ($a, $b) {
                return ($b->getWeight() - $a->getWeight()) * 10000;
            });
            foreach ($suggestions as $suggestion) {
                /** @var File $file */
                $file = $this->fileRepository->findByUid($suggestion->getFileUid());
                $suggestion->setFile($file);
            }
            $resultObject = [];
            foreach ($suggestions as $suggestion) {
                $resultObject[] = [
                    'weight' => $suggestion->getWeight(),
                    'url' => '/' . $suggestion->getFile()->getPublicUrl(),
                    'uid' => $suggestion->getFileUid(),
                ];
            }
            return new JsonResponse($resultObject);
        } catch(\Exception $exception) {
            return new JsonResponse([
                'error' => $exception->getMessage(),
            ], 500);
        }
    }
}
