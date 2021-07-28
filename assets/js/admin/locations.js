"use strict";
var FatSbLocations = {};
(function ($) {
    FatSbLocations.init = function(){
        FatSbLocations.loadLocation();
        FatSbMain.registerEventProcess($('.fat-sb-locations-container .toolbox-action-group'));
        FatSbMain.initPopupToolTip();
    };

    FatSbLocations.nameSearchOnKeyUp = function(self){
        var search_wrap = self.closest('.ui.input');
        if(self.val().length >=3 || self.val()==''){
            search_wrap.addClass('loading');
            FatSbLocations.loadLocation(function(){
                search_wrap.removeClass('loading');
            });
            if(self.val().length >=3){
                search_wrap.addClass('active-search');
            }
            if(self.val() == ''){
                search_wrap.removeClass('active-search');
            }
        }
    };

    FatSbLocations.closeSearchOnClick = function(self){
        var search_wrap = self.closest('.ui.ui-search');
        $('input',search_wrap).val('');
        $('input',search_wrap).trigger('keyup');
    };

    FatSbLocations.btAddNewOnClick = function(self){
        FatSbMain.showProcess(self);
        FatSbMain.showPopup('fat-sb-locations-template','', [],function(){
            FatSbMain.closeProcess(self);
            FatSbMain.registerEventProcess($('.fat-location-form'));
            FatSbLocations.initMap();
        });
    };

    FatSbLocations.initMap = function(){
        try {
            $('.fat-sb-google-map-wrap').each(function(){
                var map_wrap = $(this),
                    map = $('.fat-sb-google-map',map_wrap),
                    latitude_X = map.attr('data-latitude-x'),
                    latitude_Y = map.attr('data-latitude-y'),
                    map_type = map.attr('data-map-type'),
                    zoom = 15,
                    search = $('input.fat-sb-map-search-box', map_wrap),
                    input_location = $('.fat-sb-map-latitude',map_wrap);
                var mapCenter = new google.maps.LatLng(
                    latitude_X =='' ? 51.491676 : latitude_X,
                    latitude_Y=='' ? -0.167660 : latitude_Y
                );

                var google_map = new google.maps.Map(map[0], {
                    center: mapCenter,
                    zoom: zoom,
                    mapTypeId: map_type
                });

                google.maps.event.addDomListener(window, "resize", function() {
                    var center = google_map.getCenter();
                    google.maps.event.trigger(map, "resize");
                    google_map.setCenter(center);
                });

                if(latitude_X!='' && latitude_Y!=''){
                    var map_marker = new google.maps.Marker({
                        position: new google.maps.LatLng(latitude_X, latitude_Y),
                        draggable:true,
                        animation: google.maps.Animation.DROP
                    });
                    map_marker.setMap(google_map);

                    google.maps.event.addListener(map_marker, 'dragend', function(map_marker){
                        var latLng = map_marker.latLng;
                        input_location.val(latLng.lat() + ',' + latLng.lng());
                    });
                }

                search = search[0];
                var searchBox = new google.maps.places.SearchBox(search);
                google_map.controls[google.maps.ControlPosition.TOP_LEFT].push(search);

                // Bias the SearchBox results towards current map's viewport.
                google_map.addListener('bounds_changed', function() {
                    searchBox.setBounds(google_map.getBounds());
                });

                var markers = [];

                // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.
                searchBox.addListener('places_changed', function() {
                    var places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    // Clear out the old markers.
                    markers.forEach(function(marker) {
                        marker.setMap(null);
                    });
                    markers = [];

                    // For each place, get the icon, name and location.
                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function(place) {
                        if (!place.geometry) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        var icon = {
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25)
                        };

                        // Create a marker for each place.
                        var new_marker = new google.maps.Marker({
                            map: google_map,
                            icon: icon,
                            title: place.name,
                            draggable:true,
                            animation: google.maps.Animation.DROP,
                            position: place.geometry.location
                        });
                        markers.push(new_marker);

                        google.maps.event.addListener(new_marker, 'dragend', function(new_marker){
                            var latLng = new_marker.latLng;
                            input_location.val(latLng.lat() + ',' + latLng.lng());
                        });

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                        input_location.val(place.geometry.location.lat() + ',' + place.geometry.location.lng());
                    });

                    google_map.fitBounds(bounds);
                });

            })
        }catch(err){

        }
    };

    FatSbLocations.loadLocation = function(callback){
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'GET',
            data: ({
                action: 'fat_sb_get_locations',
                loc_name: $('#loc_name_search').val()
            }),
            success: function(locations){
                locations = $.parseJSON(locations);

                var template = wp.template('fat-sb-location-item-template'),
                    items = $(template(locations)),
                    elm_location = $('.fat-sb-list-locations');

                $('.column',elm_location).remove();
                $('.fat-sb-not-found').remove();
                if(locations.length>0){
                    elm_location.append(items);
                    FatSbMain.registerEventProcess($('.fat-sb-list-locations'));

                    $('.fat-item-bt-inline[data-title]','.fat-semantic-container').each(function(){
                        $(this).popup({
                            title : '',
                            content: $(this).attr('data-title'),
                            inline: true
                        });
                    });
                }else{
                    FatSbMain.showNotFoundMessage(elm_location);
                }
                if(typeof callback=='function'){
                    callback();
                }
            },
            error: function(){}
        })
    };

    FatSbLocations.showPopupLocation = function(elm){
        var loc_id = typeof elm.attr('data-id')!='undefined' ? elm.attr('data-id') : 0,
            popup_title = typeof loc_id !='undefined' ? FatSbMain.data.modal_title.edit_location : '';
        FatSbMain.showProcess(elm);
        $.ajax({
            url: fat_sb_data.ajax_url,
            type: 'POST',
            data: ({
                action: 'fat_sb_get_location_by_id',
                loc_id: loc_id
            }),
            success: function (response) {
                FatSbMain.closeProcess(elm);
                response = $.parseJSON(response);
                FatSbMain.showPopup('fat-sb-locations-template', popup_title,response,function(){
                    FatSbLocations.initMap();
                    FatSbMain.registerEventProcess($('.fat-location-form'));
                });
            },
            error: function(){}
        });
    };

    FatSbLocations.processSubmitLocation = function(self,callback){
        if(FatSbMain.isFormValid){
            FatSbMain.showProcess(self);
            var form = $('.fat-location-form .ui.form'),
                data = FatSbMain.getFormData(form),
                image_url = $('#loc_image_id img').attr('src');

            data['loc_latitude_x'] = '';
            data['loc_latitude_y'] = '';

            if($('input.fat-sb-map-latitude').val()!=''){
                data['loc_latitude_x'] = $('input.fat-sb-map-latitude').val().split(',')[0];
                data['loc_latitude_y'] = $('input.fat-sb-map-latitude').val().split(',').length==2 ? $('input.fat-sb-map-latitude').val().split(',')[1] : '';
            }

            if(typeof self.attr('data-id') !='undefined'){
                data['loc_id'] = self.attr('data-id');
            }
            $.ajax({
                url: fat_sb_data.ajax_url,
                type: 'POST',
                data: ({
                    action: 'fat_sb_save_location',
                    data: data
                }),
                success: function(response){
                    FatSbMain.closeProcess(self);
                    self.closest('.ui.modal').modal('hide');

                    response = $.parseJSON(response);
                    if(response.result >= 0){
                        var item = $('.item[data-id="' + data.loc_id + '"]','.fat-sb-list-locations');

                        data.loc_image_url = typeof image_url != 'undefined' ? image_url : '';
                        if(item.length==0){
                            data.loc_id = response.result;
                            var template = wp.template('fat-sb-location-item-template'),
                                item = $(template([data]));
                            $('.fat-sb-not-found','.fat-sb-list-locations').remove();
                            $('.fat-sb-list-locations').append(item);
                            FatSbMain.registerEventProcess(item);

                        }else{
                            $('.fat-loc-name',item).html(data.loc_name);
                            $('.fat-loc-address',item).html(data.loc_address);
                            $('.fat-loc-description',item).html(data.loc_description);
                            $('img', item).attr('src', data.loc_image_url);
                        }
                        if(typeof callback=='function'){
                            callback();
                        }

                    }else{
                        if(typeof response.message!='undefined'){
                            FatSbMain.showMessage(response.message, 3);
                        }else{
                            FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                        }
                    }
                },
                error: function(){
                    FatSbMain.closeProcess(self);
                    elm.closest('.ui.modal').modal('hide');
                    FatSbMain.showMessage(FatSbMain.data.error_message, 2);
                }
            })
        }
    };

    FatSbLocations.processDelete = function(self){
        var loc_id = self.attr('data-id');
        FatSbMain.showConfirmPopup(FatSbMain.data.confirm_delete_title,FatSbMain.data.confirm_delete_message,function($result, popup){
            if($result==1){
                var self = $('.fat-sb-bt-confirm.yes',popup);
                FatSbMain.showProcess(self);
                $.ajax({
                    url: fat_sb_data.ajax_url,
                    type: 'POST',
                    data: ({
                        action: 'fat_sb_delete_location',
                        loc_id: loc_id
                    }),
                    success: function(response){
                        FatSbMain.closeProcess(self);
                        popup.modal('hide');
                        try{
                            response = $.parseJSON(response);
                            if(response.result>0){
                                $('.item[data-id="' + loc_id + '"]','.fat-sb-list-locations').closest('.column').remove();
                            }else{
                                FatSbMain.showMessage(response.message, 2);
                            }
                        }catch(err){
                            FatSbMain.showMessage(FatSbMain.data.error_message,2);
                        }
                    },
                    error: function(){
                        FatSbMain.closeProcess(self);
                    }
                })
            }
        });
    };

    $(document).ready(function () {
        FatSbLocations.init();
    });
})(jQuery);
