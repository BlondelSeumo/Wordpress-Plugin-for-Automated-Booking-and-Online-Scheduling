<?php
/**
 * Created by PhpStorm.
 * User: roninwp
 * Date: 11/21/2018
 * Time: 9:49 AM
 */
?>
<script type="text/html" id="tmpl-fat-sb-locations-template">
    <div class="ui modal tiny fat-semantic-container fat-location-form">
        <div class="header fat-sb-popup-title"><?php echo esc_html__('Add new location','fat-services-booking'); ?></div>
        <div class="scrolling content">
            <div class="ui form">
                <div class="one fields">
                    <div class="ui image-field " id="loc_image_id" data-image-id="{{data.loc_image_id}}"
                         data-image-url="{{data.loc_image_url}}">
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="name"><?php echo esc_html__('Name','fat-services-booking');?><span class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" value="{{data.loc_name}}" name="loc_name" id="loc_name" placeholder="<?php echo esc_attr__('Location name','fat-services-booking');?>" required >
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter name','fat-services-booking');?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="address"><?php echo esc_html__('Address','fat-services-booking');?><span class="required"> *</span></label>
                        <div class="ui left icon input ">
                            <input type="text" value="{{data.loc_address}}" name="loc_address" id="loc_address" placeholder="<?php echo esc_attr__('Address','fat-services-booking');?>" required >
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter address','fat-services-booking');?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="address"><?php echo esc_html__('Link','fat-services-booking');?></label>
                        <div class="ui left icon input ">
                            <input type="text" value="{{data.loc_link}}" name="loc_link" id="loc_link" placeholder="<?php echo esc_attr__('Link','fat-services-booking');?>" >
                            <i class="edit outline icon"></i>
                        </div>
                        <div class="field-error-message">
                            <?php echo esc_html__('Please enter location link','fat-services-booking');?>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field ">
                        <label for="map">
                            <?php echo esc_html__('Map','fat-services-booking');?>
                            <div class="ui icon ui-tooltip" data-content="<?php echo esc_attr__('Please enter location to \'Enter a query\' textbox on google map for search. After search, you can drag & drop pin icon to change location','fat-services-booking');?>">
                                <i class="question circle icon"></i>
                            </div>
                        </label>
                        <div class="fat-sb-google-map-wrap">
                            <input type="hidden" class="fat-sb-map-latitude" value="{{data.loc_latitude_x}},{{data.loc_latitude_y}}">
                            <input type="text" name="map_address_search" class="fat-sb-map-search-box">
                            <div class="fat-sb-google-map"
                                 data-latitude-x="{{data.loc_latitude_x}}"
                                 data-latitude-y="{{data.loc_latitude_y}}"
                                 data-map-type="roadmap"
                                 data-zoom="15">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="one fields">
                    <div class="field">
                        <label><?php echo esc_html__('Notes','fat-services-booking');?></label>
                        <textarea rows="5" id="loc_description">{{data.loc_description}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="actions">
            <button class="ui basic button fat-close-modal">
                <i class="times circle outline icon"></i>
                <?php echo esc_html__('Cancel','fat-services-booking');?>
            </button>
            <div class="blue ui buttons">
                <div class="ui button fat-submit-modal" data-id="{{data.loc_id}}" data-onClick="FatSbLocations.processSubmitLocation">
                    <i class="save outline icon"></i>
                    <?php echo esc_html__('Save','fat-services-booking');?>
                </div>
            </div>
        </div>
    </div>
</script>
<script type="text/html" id="tmpl-fat-sb-location-item-template">
    <# _.each(data, function(item){ #>
        <div class="four wide column">
            <div class="ui items ">
                <div class="item fat-pd-10 fat-border-spin fat-hover fat-hover-link" data-id="{{item.loc_id}}">
                    <div class="ui tiny image">
                        <# if (item.loc_image_url!=''){ #>
                        <img class="fat-border-round fat-box-shadow fat-img-80" src="{{item.loc_image_url}}"
                             data-image-id="{{item.loc_image_id}}">
                        <# }else{ #>
                        <span class="fat-no-thumb fat-img-80"></span>
                        <# } #>
                    </div>
                    <div class="content">
                        <div class="header thin">
                          <span class="fat-loc-name">{{item.loc_name}}  ( ID: {{item.loc_id}})</span>
                        </div>

                        <div class="meta">
                            <span class="fat-loc-address">{{item.loc_address}}</span>
                        </div>
                        <div class="fat-loc-description">
                            {{item.loc_description}}
                        </div>
                    </div>
                    <button class=" ui icon button fat-item-bt-inline fat-sb-delete" data-onClick="FatSbLocations.processDelete"
                            data-id="{{item.loc_id}}" data-title="<?php echo esc_attr__('Delete','fat-services-booking');?>">
                        <i class="trash alternate outline icon"></i>
                    </button>

                    <button class=" ui icon button fat-item-bt-inline fat-sb-edit" data-onClick="FatSbLocations.showPopupLocation"
                            data-id="{{item.loc_id}}" data-title="<?php echo esc_attr__('Edit','fat-services-booking');?>">
                        <i class="edit outline icon"></i>
                    </button>
                </div>
            </div>
        </div>
    <# }) #>
</script>