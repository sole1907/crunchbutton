var Orm = {

	properties: function() {
		var properties = {};
		for (var name in this) {
			if (name.indexOf('_') !== 0 && !$.isFunction(this[name])) {
				if (this[name] === true) {
					properties[name] = 1;
				} else if (this[name] === false) {
					properties[name] = 0;
				} else {
					properties[name] = this[name];
				}
			}
		}
		return properties;
	},

	loadType: function(cls, data) {
		var self = this;
		if (!self['__' + data]) {
			self['__' + data] = [];
			for (x in self['_' + data]) {
				self['__' + data][self['__' + data].length] = App.cache(cls, self['_' + data][x]);
			}
			self['_' + data] = null;
		}
		return self['__' + data];
	},

	save: function(complete) {
		if (!this.type) return;
		$.post(App.service + this.resource + (this.id ? ('/' + this.id) : ''), this.properties(), function(result) {
			if (complete) {
				complete(result);
			}
		});
	},

	load: function(id) {
		var self = this;
		this.cachedAt = ( Math.floor( new Date().getTime() / 1000 ) );
		if (typeof(id) == 'object') {
			this.finished(id);
		} else {
			App.request(App.service + self.resource + '/' + id, function(json) {
				if (json.error) {
					throw 'ORM load error: ' + json.error + "\nType: " + self.type + "\nResource: " + self.resource + "\nID: " + id;
				} else {
					self.finished(json);
				}
			}, function() {
				if (typeof self.loadError == 'function') {
					self.loadError.apply(self, arguments);
				}
			});
		}
	}
}