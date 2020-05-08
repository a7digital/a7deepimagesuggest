define(function() {
  class AdvancedError extends Error {
    constructor(message, cause) {
      super(message);
      this.cause = cause;
      if (this.cause && this.cause instanceof AdvancedError)
        this.rootCause = this.cause.rootCause;
      else
        this.rootCause = this;
    }
    toString() {
      if (this.cause)
        return this.cause.toString() + ' ⇒ ' + this.message;
      return this.message;
    }
  }
  return AdvancedError;
});
