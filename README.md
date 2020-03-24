# a7picsuggest
Provides image suggestions to editors via Deep Learning

## State

***!!! Currently this extension is under development and not ready to be used in any way !!!***

The extension will hopefully be released in early may 2020.

## What it _will_ provide

The main goal is to provide editors some assistance when they select images for their content elements. The first part will be to provide suggestions from the images located directly on the server, for this it will be necessary to tag images in advance using the [a7pictags](https://github.com/a7digital/a7pictags). This will however most likely be extended by more image suggestions from at least Pixabay and maybe more other general image providing platforms.

The suggestions will appear only in content elements of appropriate type (i.e. image, text with image and media elements) and will consider as context for the image suggestions:

 * The title and subtitle of the content element
 * The text content of the content element
 * The title of the page
 * The other contents of the page
 * Some context that can be given via extension configuration
