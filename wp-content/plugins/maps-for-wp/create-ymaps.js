jQuery(function($){$(document).ready( function() {
	
 console.log("Подключен скрип настроек плагина create-ymaps.js!");
 if (typeof mfwp_setings !=="undefined"){console.table(mfwp_setings); ymaps.ready(init); /* ждем загрузки API Яндекс карт*/} 
	else {console.log("create-ymaps.js: массив настроек mfwp_setings в файл не передан!");}

 function init(){ // начинаем инициализацию карт
	console.log("Сработала функция init");
	if ($("div").is("#yamapCreate")) {
		console.log("На этой странице есть карта для редактирования поста");
		initializeMap_Create();
	}
	if ($("div").is(".yamapOnePoint")) {
		var countOne = $('.yamapOnePoint2').length;
		console.log("На этой странице есть карта с одной точкой. Всего их "+countOne);
		
		for (var i =0; i < mfwp_setings_OnePoint2.length; i++) {
			mfwp_setings_OnePoint = mfwp_setings_OnePoint2[i];
			console.table(mfwp_setings_OnePoint);
			initializeMap_OnePoint();
		}
	}
	if ($("div").is("#yamapManyPoints")) {
		console.log("На этой странице есть карта с множеством точек"); console.table(mfwp_setings_ManyPoints);
		if (typeof mfwp_setings_ManyPoints !=="undefined") {initializeMap_ManyPoints();}
	}	
 }

function initializeMap_Create () {
 var latitude = $('#mfwp_lat').val();
 var longitude = $('#mfwp_lon').val();
 var locations = $('#mfwp_locat').val(); 
 if ( locations !== '' ) {var nazvanie = locations;} else {var nazvanie = 'Объект';}

 if ( latitude !== '' && longitude !=='' ) {
	myMap = new ymaps.Map("yamapCreate", {
		center: [latitude, longitude],
		zoom: 14
		}); //создаем карту
		myPlacemark = new ymaps.Placemark([latitude, longitude],{
		iconContent: nazvanie //'Объект'
        }, {
            preset: 'islands#violetStretchyIcon',
            draggable: true
        }); // создаем точку
 } else { console.log("ранее созданной карты нет");
	myMap = new ymaps.Map("yamapCreate", {
		center: [mfwp_setings[0].mfwp_lat_Create, mfwp_setings[0].mfwp_long_Create],
		zoom: 14
	}); //создаем карту
	myPlacemark = new ymaps.Placemark([mfwp_setings[0].mfwp_lat_Create, mfwp_setings[0].mfwp_long_Create],{
	iconContent: 'поиск...'
	}, {
		preset: 'islands#violetStretchyIcon',
		draggable: true
		});
	$('#mfwp_lat').val(mfwp_setings[0].mfwp_lat_Create); //выводим адрес указанный в настройках плагина
	$('#mfwp_lon').val(mfwp_setings[0].mfwp_long_Create); //выводим адрес указанный в настройках плагина
 }
 
 myMap.geoObjects.add(myPlacemark); //ставим точку на карту
    // Слушаем клик на карте
	myMap.events.add('click', function (e) {
	var coords = e.get('coords');

	// Если метка уже создана – просто передвигаем ее
	if (myPlacemark) {
		myPlacemark.geometry.setCoordinates(coords);			
		var curResult = String(coords);
		var curResultArr = curResult.split(',');
		// забиваем долготу и широту в input
		$('#mfwp_lat').val(curResultArr[0]);
		$('#mfwp_lon').val(curResultArr[1]);
	}
	// Если нет – создаем.
	else {
		myPlacemark = createPlacemark(coords);
		myMap.geoObjects.add(myPlacemark);
		// Слушаем событие окончания перетаскивания на метке.
		myPlacemark.events.add('dragend', function () {
			getAddress(myPlacemark.geometry.getCoordinates());
		});
	}
	getAddress(coords);
    });

	// Создание метки
	function createPlacemark(coords) {
		return new ymaps.Placemark(coords, {
		iconContent: 'поиск...'
		}, {
			preset: 'islands#violetStretchyIcon',
			draggable: true
		});
	}

	// Определяем адрес по координатам (обратное геокодирование)
	function getAddress(coords) {
		myPlacemark.properties.set('iconContent', 'поиск...');
		ymaps.geocode(coords).then(function (res) {
			var firstGeoObject = res.geoObjects.get(0);	
			$('#mfwp_locat').val(firstGeoObject.properties.get('name')); //выводим адрес
			
			$('#mfwp_country').val(firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.CountryName')); //выводим адрес
			$('#mfwp_region').val(firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.AdministrativeAreaName')); //выводим адрес
			$('#mfwp_subadministrative').val(firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.SubAdministrativeAreaName')); //выводим адрес			
			$('#mfwp_city').val(firstGeoObject.properties.get('metaDataProperty.GeocoderMetaData.AddressDetails.Country.AdministrativeArea.SubAdministrativeArea.Locality.LocalityName')); //выводим адрес	
			myPlacemark.properties
				.set({
					iconContent: firstGeoObject.properties.get('name'),
					balloonContent: firstGeoObject.properties.get('text')
				});  
		});
	}
}

function initializeMap_OnePoint () {
	
	var tt = "yamapOnePoint"+mfwp_setings_OnePoint[0].mfwp_id;
	
	var latitude = mfwp_setings_OnePoint[0].mfwp_latitude;
	var longitude = mfwp_setings_OnePoint[0].mfwp_longitude;	
		myMap_OnePoint = new ymaps.Map(tt, {
			center: [latitude, longitude],
			zoom: mfwp_setings_OnePoint[0].mfwp_zoom
		})
	myPlacemark_OnePoint = new ymaps.Placemark([latitude, longitude], {
		hintContent: mfwp_setings_OnePoint[0].hintContent,//'Тут расположен данный объект!',
		balloonContent: 'Местоположение может быть указано не точно'
	}, {
		// Опции. Необходимо указать данный тип макета.
		iconLayout: 'default#image',
		// Своё изображение иконки метки.
		iconImageHref: mfwp_setings_OnePoint[0].iconImageHref,//'/wp-content/plugins/realty-7/img/marker.png',
		// Размеры метки.
		iconImageSize: [36, 36],
		// Смещение левого верхнего угла иконки относительно её "ножки" (точки привязки).
		iconImageOffset: [-3, -42]
		});   
	myMap_OnePoint.geoObjects.add(myPlacemark_OnePoint);
}



function initializeMap_ManyPoints () {

	myMap_ManyPoints = new ymaps.Map("yamapManyPoints", {
		center: [mfwp_setings_ManyPoints[0].mfwp_lat_centerManyPoints, mfwp_setings_ManyPoints[0].mfwp_long_centerManyPoints],
		zoom: mfwp_setings_ManyPoints[0].mfwp_zoom_ManyPoints
	}); 
	
	mfwp_setings_ManyPoints.forEach(function(index){
	
	myPlacemark_ManyPoints = new ymaps.Placemark([index.latitude, index.longitude], {
				// всплывает при наведении
                hintContent: index.thumbnail+index.the_title,
				// всплывает при нажатии
				balloonContentHeader: '<h1>'+index.the_title+'</h1>',
				balloonContentBody: index.thumbnail,
			//	balloonContentFooter: '<h1>Привет5</h1>'
				//balloonContent: index.id // игнорируется, если есть balloonContentBody
				//iconContent: index.thumbnail,
				
		}, {
            // Опции.
            // Необходимо указать данный тип макета.
            iconLayout: 'default#image',
            // Своё изображение иконки метки.
            iconImageHref: mfwp_setings_ManyPoints[0].iconImageHref,//'/wp-content/plugins/realty-7/img/marker.png',
            // Размеры метки.
            iconImageSize: [36, 36],
            // Смещение левого верхнего угла иконки относительно
            // её "ножки" (точки привязки).
            iconImageOffset: [-3, -42]
        });
            
            myMap_ManyPoints.geoObjects.add(myPlacemark_ManyPoints);
 
		}); 

		clustersOnMap = new Array();
}
})});