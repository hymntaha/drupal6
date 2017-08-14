<!-- 

/* 
    A Better Form - A jQuery plugin
    ==================================================================
    ©2010 JasonLau.biz - Version 1.1.0
    ==================================================================
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

(function($){
    
 	$.fn.extend({ 
 	  
 		abform: function(options){
 		 
			var defaults = {
			    attributes : 'id="my-form" action="#" method="post"',
                sequential_disable : true,
                pluggable : false,
                serialized : true,
                multipart : false,
                clickonce : true,
                filtertext : true,
                textfilters : 'url=,link=,http:,www.,href,<a',
                convert : false,
                obj :  $(this)                    
			}
            				
			var options =  $.extend(defaults, options);
            var obj = $(this); 
                      
    		return this.each(function(){
    		  
				var o = options,
                attributes = o.attributes,
                sequential_disable = o.sequential_disable,
                pluggable = o.pluggable,
                serialized = o.serialized,
                multipart = o.multipart,
                clickonce = o.clickonce,
                filtertext = o.filtertext,
                textfilters = o.textfilters,
                convert = o.convert,
                all_ids = [];
                var id = obj.attr('id');
                
                if(convert){
                    
                    var elements = convert.split('{');
                    
                    for(var i in elements){
                      if(i > 0){
                        var element = elements[i].split('}');
                      element = element[0];
                      
                      var e = element.split('|');
                      var e_id = (!e[0]) ? alert('Error! Code misconfiguration. Disable A Better Form convert option or check the documentation to learn how to properly code the convert option. You are missing the element id option.') : e[0];
                      var e_type = (!e[1]) ? alert('Error! Code misconfiguration. Disable A Better Form convert option or check the documentation to learn how to properly code the convert option. You are missing the element type option.') : e[1];    
                      var e_attributes = (e[2]) ? ' ' + e[2] : '';
                      var e_value = $("#" + e_id).html();
                      
                      var newElement = '';
                      
                      switch(e_type){
                        
                        case 'text':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'textarea':                        
                        newElement = '<textarea id="' + e_id + '" name="' + e_id + '"' + e_attributes + '>' + e_value + '</textarea>';
                        break;
                        
                        case 'password':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'file':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'button':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'submit':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'reset':                   
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'image':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" src="' + e_value + '"' + e_attributes + ' />';
                        break;
                                                
                        case 'radio':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'checkbox':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        case 'select':                        
                        newElement = '<select id="' + e_id + '" name="' + e_id + '"' + e_attributes + '>';
                        $("#" + e_id + " ul").each(function(){
                            if($(this).attr('title')){
                              newElement += '<optgroup label="' + $(this).attr('title') + '">';  
                            }                       
                            
                        $("#" + e_id + " ul li").each(function(){
                            newElement += '<option value="' + $(this).attr('id') + '">' + $(this).html() + '</option>'; 
                        });
                        
                        if($(this).attr('title')){
                              newElement += '</optgroup>';  
                            }
                        });
                        newElement += '</select>'; 
                        
                        break;
                        
                        case 'hidden':                        
                        newElement = '<input id="' + e_id + '" name="' + e_id + '" type="' + e_type + '" value="' + e_value + '"' + e_attributes + ' />';
                        break;
                        
                        
                      }
                      
                      $("#" + e_id).replaceWith(newElement);
                                            
                      }                    
                  }
                }
                
                var all_fields = $('#'+id+' input, #'+id+' textarea, #'+id+' select');
                $('#'+id+' input, #'+id+' textarea, #'+id+' select').each(function(){
                    
                all_ids.push($(this).attr('id'));
                
                if($(this).is('input') && ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox')){
                    
                    $(this).attr('name',$(this).attr('id'));
                    
                    }
                
                });
                
                if(sequential_disable){
                    
                   all_fields.attr('disabled', 'disabled');
                   all_fields.first().attr('disabled', '');
                   seq_dis(id, all_ids);
                    
                }
                                
                $('#'+id+' input, #'+id+' textarea, #'+id+' select').each(function(a){
                    
                    var next_item = a+1;
                    var next_id = $('#'+all_ids[next_item]).attr('id'); 
  
                    if($(this).is('select') || ($(this).is('input') && ($(this).attr('type') == 'file' || $(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox'))){
                        
                        $(this).bind('change',function(){
                                                       
                            if($(this).val() != ''){
                                
                                $(this).attr('name',$(this).attr('id'));
                                $('#'+all_ids[next_item]).attr('disabled', '');
                                                                            
                            } else {
                                
                                if($(this).attr('type') != 'radio' || $(this).attr('type') != 'checkbox'){
                                    
                                $(this).attr('name','');  
                                
                                }                              
                             
                            }
                            
                            if(sequential_disable){ seq_dis(id, all_ids); }
                                                    
                        });
                    }
                    
                    if($(this).is('input, textarea') && $(this).attr('type') != 'file' && $(this).attr('type') != 'radio' && $(this).attr('type') != 'checkbox'){
                        
                        $(this).bind('keyup change',function(){
                            
                            if($(this).val() != ''){
                                
                                if(filtertext){
                                    
                                  var t_filters = textfilters.split(',');
                                  
                                  for(var i in t_filters){
                                    
                                    var checkit = $(this).val().split(t_filters[i]);
                                    
                                    if(checkit.length > 1){
                                        
                                        $(this).val(checkit[0]);
                                        
                                    }
                                  }  
                                    
                                }
                                                                
                                $('#'+all_ids[next_item]).attr('disabled', '');
                                $(this).attr('name',$(this).attr('id')); 
                                
                            } else {
                                
                                $(this).attr('name','');
                                                                 
                            }
                            
                            if(sequential_disable){ seq_dis(id, all_ids); } 
                                                   
                        });
                    } 
                                       
                });
                
                $('#'+id+' optgroup').each(function(i){
                    
                    if($(this).hasClass('aboptgroup')){
                        
                        var this_html = $(this).html();
                        $(this).replaceWith('\n<optgroup label="' + $(this).attr('id') +'">\n'
                                            + this_html
                                            + '\n</optgroup>\n');  
                        $(this).attr('label',$(this).attr('id'));
                        
                    } else {
                        
                        $(this).attr('name',$(this).attr('id'));
                                               
                    }
                });
                                
                $('.absubmit').bind('click',function(){ 
                    if($(this).attr('disabled') != 'disabled' && $(this).parent().attr('id') == obj.attr('id')){
                        $('#'+id+' input').each(function(){
                            if($(this).attr('type') == 'hidden'){
                              $(this).attr({
                                disabled : '',
                                name : $(this).attr('id')
                                });  
                            }
                        });
                        var innerWrap = (multipart) ? "<form " + attributes + " enctype=\"multipart/form-data\"></form>" : "<form " + attributes + "></form>" ;
                        
                        obj.wrapInner(innerWrap);
                        
                        if(pluggable && $.isFunction(pluggable)){
                            
                            if(serialized){
                                
                                pluggable($(this).parent().serialize());
                                 
                            } else {
                                
                                pluggable();
                                
                            }                      
                            
                        } else {
                            
                           $(this).parent().submit(); 
                           
                        }
                    }
                    
                    if(clickonce){
                        
                       $(this).attr('disabled','disabled'); 
                        
                    }
                     
                });
    		});
            
            function seq_dis(id, all_ids){
                $('#'+id+' input, #'+id+' textarea, #'+id+' select').each(function(b){
                    
                    var sd = false;
                    
                    for(var c in all_ids){
                        
                        if(c < b){
                            
                            if($('#'+all_ids[c]).val() == ''){
                                
                                sd = true;
                                
                            }
                        }
                    }
                    
                    if(sd){
                        
                        $('#'+all_ids[b]).attr('disabled', 'disabled');
                        
                    } else {
                        
                        if($(this).is('input') && ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox')){
                            
                          $('[id=' + $(this).attr('id') + ']').each(function(){
                            
                            $(this).attr('disabled', '');
                            
                          }); 
                           
                        } else {
                            
                          $('#'+all_ids[b]).attr('disabled', '');  
                          
                        }
                        
                    } 
                                          
                });
            };
                                        
 		}
	});	
})(jQuery);

 -->