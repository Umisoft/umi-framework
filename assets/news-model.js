$(function() {
	ko.observableArray.fn.pushAll = function(valuesToPush) {
		var underlyingArray = this();
		this.valueWillMutate();
		ko.utils.arrayPushAll(underlyingArray, valuesToPush);
		this.valueHasMutated();
		return this;
	};

	var responseJson= 0;
	var Section = function (name, selected, pullAction, isHold) {
		this.name = name;
		this.isSelected = ko.computed(function() {
			return this === selected();
		}, this);
		this.items = ko.observableArray([]);
		if(!isHold){
			(function(self){
				responseJson ++;
				return $.ajax({
					url: 'http://www.umi-cms.ru/udata://news/lastlist/('+ pullAction +')//4.json',
					type: 'GET',
					dataType: 'jsonp',
					jsonpCallback: 'response_' + responseJson + '',
					data: {'json-callback': 'response_' + responseJson +''},
					success: function(data) {
						var collection = [],
							key;
						for(key in data.items.item){
							collection.push(data.items.item[key]);
						}
						return self.items.pushAll(collection);
					}
				});
			}(this));
		}
	};

	var SectionViewModel = function () {
		var self = this;
		self.selectedSection = ko.observable();
		self.sections = ko.observableArray([
			new Section('Новости', self.selectedSection, '/company/news'),
			new Section('Акции', self.selectedSection, '/buy/promo'),
			new Section('Get Involved', self.selectedSection , 'events', true)
		]);
		//inialize to the first section
		self.selectedSection(self.sections()[0]);
	}

	ko.applyBindings(new SectionViewModel());
});