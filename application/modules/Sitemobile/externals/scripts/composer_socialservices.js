/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Sitemobile
 * @copyright  Copyright 2012-2013 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: composer_socialservices.js 6590 2013-06-03 00:00:00Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

(function() { // START NAMESPACE
  var $ = 'id' in document ? document.id : window.$;
  
  sm4.socialService = {
    
    initialize : function(element) {  
      
      if ($.type(element) != 'undefined') {
        this[element].toggle(this);
        if (element == 'facebook')
          fb_loginURL = '';
        else if (element == 'twitter')
          twitter_loginURL = '';
        else 
          linkedin_loginURL = '';
      }
      else {
       //Adding evet to Facebook icon
        $('.composer_facebook_toggle').on('click',function(){ 
          this['facebook'].toggle(this);
        }.bind(this));

        //Adding event to Twiiter icon
        $('.composer_twitter_toggle').on('click',function(){
          this['twitter'].toggle(this);
        }.bind(this));

        //Adding event to Linkedin icon
        $('.composer_linkedin_toggle').on('click',function(){
          this['linkedin'].toggle(this);
        }.bind(this));
      }
    },
    
    facebook:  {
      
      name : 'facebook',

      options : {
        title : '',
        lang : {
          'Publish this on Facebook': '',
          'Do not publish this on Facebook': ''
        },
        requestOptions : false
      },

      initialize : function(options) { 
        this.elements = {};
        this.params = {};
        
		
      },
      
      toggle : function(event) { 
        
        if (fb_loginURL == '') {   
          var checkBox = $('.compose-form-input-facebook');
           checkBox.each(function(key, el){
              if ($(el).attr("checked") == 'checked') { 
                $(el).removeAttr('Checked');
              }
              else {
                $(el).attr("checked", "checked");
              }
           });         
          
          $('.composer_facebook_toggle').toggleClass('composer_facebook_toggle_active'); 		 
          if (!$('.composer_facebook_toggle').hasClass('composer_facebook_toggle_active')) { 
            
           // $('#composer_facebook_toggle').children('span').html(this.options.lang['Publish this on Facebook'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
          else {
          //  $('#composer_facebook_toggle').children('span').html(this.options.lang['Do not publish this on Facebook'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
        }
        else { 
           window.open(fb_loginURL, '_blank');
        }
      }
      
    },
    
    twitter: {
      name : 'twitter',

      options : {
        title : '',
        lang : {
          'Publish this on Twitter': '',
          'Do not publish this on Twitter': ''
        },
        requestOptions : false
      },

      initialize : function(options) { 
        this.elements = {};
        this.params = {};
        
		
      },
      
      toggle : function(event) { 
        
        if (twitter_loginURL == '') {  
          var checkBox = $('.compose-form-input-twitter');
           checkBox.each(function(key, el){
              if ($(el).attr("checked") == 'checked') { 
                $(el).removeAttr('Checked');
              }
              else {
                $(el).attr("checked", "checked");
              }
           }); 
            
          $('#composer_twitter_toggle').toggleClass('composer_twitter_toggle_active'); 		 
          if (!$('#composer_twitter_toggle').hasClass('composer_twitter_toggle_active')) { 
            
           // $('#composer_twitter_toggle').children('span').html(this.options.lang['Publish this on Twitter'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
          else {
          //  $('#composer_twitter_toggle').children('span').html(this.options.lang['Do not publish this on Twitter'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
        }
        else { 
           window.open(twitter_loginURL, '_blank');
        }
      }
      
      
    },
    
    linkedin: {     
      
      name : 'linkedin',

      options : {
        title : '',
        lang : {
          'Publish this on Linkedin': '',
          'Do not publish this on Linkedin': ''
        },
        requestOptions : false
      },

      initialize : function(options) { 
        this.elements = {};
        this.params = {};
        
		
      },
      
      toggle : function(event) { 
        
        if (linkedin_loginURL == '') {  
          var checkBox = $('.compose-form-input-linkedin');
           checkBox.each(function(key, el){
              if ($(el).attr("checked") == 'checked') { 
                $(el).removeAttr('Checked');
              }
              else {
                $(el).attr("checked", "checked");
              }
           }); 
              
         $('#composer_linkedin_toggle').toggleClass('composer_linkedin_toggle_active'); 		 
          if (!$('#composer_linkedin_toggle').hasClass('composer_linkedin_toggle_active')) { 
            
           // $('#composer_linkedin_toggle').children('span').html(this.options.lang['Publish this on Linkedin'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
          else {
         //   $('#composer_linkedin_toggle').children('span').html(this.options.lang['Do not publish this on Linkedin'] + '<img alt="" src="application/modules/Advancedactivity/externals/images/tooltip-arrow-down.png" />');
          }
        }
        else { 
           window.open(linkedin_loginURL, '_blank');
        }
      }
    }
    
    
  } 
  

})(); // END NAMESPACE

sm4.core.runonce.add(function() { 
  if (typeof sm4 != 'undefined')
   sm4.socialService.initialize();
 
}); 