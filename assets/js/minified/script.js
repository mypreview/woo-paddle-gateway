"use strict";(function($,Paddle,l10n){"use strict";var script={cache:function cache(){this.els={};this.vars={};this.vars.wc=".woocommerce";this.vars.errorContainer=".woocommerce-NoticeGroup.woocommerce-NoticeGroup-checkout";this.els.$form=$("form.woocommerce-checkout");this.els.$gateway=$("#payment_method_woo-paddle-gateway")},init:function init(){this.cache();this.setup();this.bindEvents()},setup:function setup(){if(l10n.is_sandbox){Paddle.Environment.set("sandbox")}if(!l10n.vendor_id){return}Paddle.Setup({vendor:Number(l10n.vendor_id)})},bindEvents:function bindEvents(){this.els.$form.on("submit",this.handleOnSubmit)},handleOnSubmit:function handleOnSubmit(event){if(!script.els.$gateway.is(":checked")){return}event.preventDefault();var $form=$(this);if($form.is(".processing")){return false}Paddle.Spinner.show();$.ajax({type:"POST",async:true,dataType:"json",url:l10n.checkout_uri,data:script.els.$form.serialize(),success:function success(response){$form.unblock();$(script.vars.errorContainer).remove();try{if("success"===response.result){Paddle.Checkout.open({email:response.customer_email,country:response.customer_country,override:response.generate_pay_link,disableLogout:true,method:"overlay",displayModeTheme:"light"})}else if("failure"===response.result){throw"Result failure"}else{throw"Invalid response"}}catch(err){if(true===response.reload){window.location.reload();return}if(true===response.refresh){$(document.body).trigger("update_checkout")}$(script.vars.errorContainer).remove();if(response.messages){$form.prepend(response.messages)}$form.removeClass("processing").unblock();$form.find(".input-text, select").blur();Paddle.Spinner.hide()}}})}};script.init()})(jQuery,window.Paddle,woo_paddle_gateway_admin_params);