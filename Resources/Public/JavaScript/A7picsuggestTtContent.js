define(['TYPO3/CMS/A7picsuggest/AdvancedError'], function (AdvancedError) {
  class FieldTextualValue {
    constructor(field, weight) {
      /** @type {HTMLInputElement|HTMLTextAreaElement} */
      this.field = field;
      this.formEngineField = document.querySelector(`input[data-formengine-input-name="${this.field.name}"]`);
      this.weight = weight;
      this.onChange = [];
      let resolveInitialize;
      this.initializing = new Promise(resolve => resolveInitialize = resolve);

      this.field.addEventListener('change', this._valueChange.bind(this));
      if (this.formEngineField) {
        this.formEngineField.addEventListener('change', this._valueChange.bind(this));
        const checkInitializationStatus = () => {
          if (this.formEngineField.dataset.formengineInputInitialized) {
            resolveInitialize();
          } else {
            setTimeout(checkInitializationStatus, 100);
          }
        };
        setTimeout(checkInitializationStatus, 100);
      } else {
        resolveInitialize();
      }
    }
    _valueChange() {
      for (const observer of this.onChange)
        observer();
    }
    getPrevalence(tag) {
      const field = this.formEngineField === null ? this.field : this.formEngineField;
      const regex = tag.title.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
      let score = 0;
      {
        const matches = field.value.match(new RegExp('\\b' + regex + '\\b', 'ig'));
        score += 0.5 * (matches === null ? 0 : matches.length);
      }
      {
        const matches = field.value.match(new RegExp(regex, 'ig'));
        score += 0.3 * (matches === null ? 0 : matches.length);
      }
      return score;
    }
  }
  class TextualContext {
    /**
     * @param {FieldTextualValue[]} values
     */
    constructor(values) {
      this.values = values;
      this.onValueChange = [];
      this._tagList = null;
      const initializingPromises = [];
      for (const value of this.values) {
        value.onChange.push(this._valueChange.bind(this));
        initializingPromises.push(value.initializing);
      }
      this.initializing = Promise.all(initializingPromises);
    }
    _valueChange() {
      for (const observer of this.onValueChange)
        observer();
    }
    getTags() {
      if (this._tagList === null) {
        return fetch(TYPO3.settings.ajaxUrls['a7picsuggest-give-tags']).catch(error => { throw new AdvancedError(`Ajax request to get tags failed.`, error); })
          .then(response => response.json()).catch(error => { throw new AdvancedError(`Ajax response for tag retrieval could not be JSON decoded.`, error); })
          .then(jsonResponse => {
            this._tagList = jsonResponse;
            return jsonResponse;
          });
      }
      return Promise.resolve(this._tagList);
    }
    suggestTags() {
      return this.getTags()
        .then(tags => {
          const tagOccurrences = {};
          for (const tag of tags) {
            tagOccurrences[tag.uid] = 0;
            for (const value of this.values) {
              tagOccurrences[tag.uid] += value.getPrevalence(tag) * value.weight;
            }
          }
          const suggestedTags = [];
          for (const uid in tagOccurrences) if (tagOccurrences.hasOwnProperty(uid)) {
            suggestedTags.push({
              weight: tagOccurrences[uid],
              uid: uid,
            });
          }
          return suggestedTags;
        });
    }
  }
  class SuggestionDemander {
    /**
     * @param {HTMLElement} element
     * @param {TextualContext} context
     */
    constructor(element, context) {
      this.element = element;
      this.context = context;
      this.weightPercentageFormatter = new Intl.NumberFormat("en-US", {
        style: 'decimal',
        minimumFractionDigits: 1,
        maximumSignificantDigits: 3,
      });
    }
    initialize() {
      this.context.onValueChange.push(this.updateSuggestions.bind(this));
      this.updateSuggestions();
    }
    makeSuggestionElement(suggestion) {
      const container = document.createElement('div');
      container.classList.add('suggestion');

      const img = document.createElement('img');
      img.src = suggestion.url;
      container.append(img);

      const weight = document.createElement('span');
      weight.textContent = this.weightPercentageFormatter.format(suggestion.weight);
      weight.classList.add('weight');
      container.append(weight);

      return container;
    }
    updateSuggestions() {
      this.context.suggestTags()
        .then(tags => this.loadSuggestions(tags)).catch(error => { throw new AdvancedError(`Could not load image suggestions.`, error); })
        .then(suggestions => {
          while (this.element.children.length)
            this.element.firstChild.remove();
          for (const suggestion of suggestions) {
            const element = this.makeSuggestionElement(suggestion);
            this.element.append(element);
          }
        }).catch(error => {
          top.TYPO3.Notification.error(`Image suggestion error`, `Cannot show suggestions. ${error}`);
          if (error instanceof AdvancedError)
            throw error.rootCause;
          else
            throw error;
        });
    }
    loadSuggestions(tags) {
      const formData = new FormData();
      formData.set('tags', JSON.stringify(tags));
      return fetch(TYPO3.settings.ajaxUrls['a7picsuggest-suggest'], {
          method: 'POST',
          body: formData,
        }).catch(error => { throw new AdvancedError(`AJAX request to load suggestions failed.`, error); })
        .then(response => response.json()
          .then(json => {
            if (response.ok)
              return json;
            else
              throw new AdvancedError(json.error);
          })
        );
    }
  }
  function init() {
    const textualValues = [];
    for (const headerField of document.querySelectorAll('input[name^="data"][name$="[header]"]'))
      textualValues.push(new FieldTextualValue(headerField, 1.0));
    for (const subHeaderField of document.querySelectorAll('input[name^="data"][name$="[subheader]"]'))
      textualValues.push(new FieldTextualValue(subHeaderField, 0.8));
    for (const bodyTextField of document.querySelectorAll('textarea[name^="data"][name$="[bodytext]"]'))
      textualValues.push(new FieldTextualValue(bodyTextField, 0.4));
    const context = new TextualContext(textualValues);
    for (const suggestionDemandingArea of document.querySelectorAll('.a7picsuggest-suggestions')) {
      const suggestionDemander = new SuggestionDemander(suggestionDemandingArea, context);
      context.initializing.then(suggestionDemander.initialize());
    }
  }
  if (document.readyState !== 'loading')
    init();
  else
    document.addEventListener('DOMContentLoaded', init);
});
