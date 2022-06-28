jQuery(document).ready(function($) {
    const OTPTIME = parseInt($("#wpas_payamito_awesome_support_otp_time").val());
    const OSENDOTP = $("#wpas_payamito_awesome_support_send_otp")[0];
    const NOUNCE = $("#wpas_payamito_awesome_support_nonce").val();
    const OTP = $("#wpas_payamito_awesome_support_otp");
    if (OTPTIME !== undefined && OSENDOTP !== undefined && NOUNCE !== undefined && OTP !== undefined) {

        const FIELD=$("#wpas_payamito_awesome_support_phone_number");
        
        if (FIELD !== undefined) {
            $(OSENDOTP).click(function() {

                if (validate_field(FIELD)) {
                    let data = { "phone_number": FIELD.val() };
                    Spinner(type = "start");
                    $.ajax({
                            url: payamito_awesome_support_general.ajaxurl,
                            type: 'POST',
                            data: {
                                'action': "payamito_awesome_support",
                                'nonce': payamito_awesome_support_general.nonce,
                                "phone_number": FIELD.val(),
                               
                            }
                        }).done(function(r, s) {
                          
                            if (s == 'success' && r != '0' && r != "" && typeof r === 'object') {
                                notification(r.e, r.message)
                                if (r.e == 1) {
                                    timer();
                                }
                            }
                        }).fail(function() {

                        })
                        .always(function(r, s) {
                            Spinner(type = "close");
                        });
                }

            });

           
            function validate_field(field) {
                
                $([document.documentElement, document.body]).animate({
                    scrollTop: field.offset().top - 35
                }, 1000);
                field.addClass("-error-field")
                if (field.val() === null || !field.val().trim().length) {
                    notification(0, payamito_awesome_support_general.invalid)
                    return false;
                }
                return true;
            }

            function notification(ty = -1, m) {
                switch (ty) {
                    case ty = -1:
                        iziToast.error({
                            timeout: 10000,
                            title: payamito_awesome_support_general.error,
                            message: m,
                            displayMode: 2
                        });
                        break;
                    case ty = 0:
                        iziToast.warning({
                            timeout: 10000,
                            title: payamito_awesome_support_general.warning,
                            message: m,
                            displayMode: 2
                        });
                        break;
                    case ty = 1:
                        iziToast.success({
                            timeout: 10000,
                            title: payamito_awesome_support_general.success,
                            message: m,
                            displayMode: 2
                        });
                }
            }

            function Spinner(type = "start") {
                let spinner = $("body");
                if (type == "start") {
                    $.LoadingOverlay("show",{'progress':true});
                    $("form").bind("keypress", function(e) {
                        if (e.keyCode == 13) {
                            return false;
                        }
                    });
                } else {
                    $.LoadingOverlay("hide");
                }
            }

            function timer() {
                
                var timer = OTPTIME;
                var innerhtml = OSENDOTP.value;
                $("#wpas_payamito_awesome_support_send_otp").prop('disabled', true);
                var Interval = setInterval(function() {
                   
                    seconds =parseInt(timer) ;
                    seconds = seconds < 10 ? "0" + seconds : seconds;
                    OSENDOTP.value = seconds + ":" +
                        payamito_awesome_support_general.second;
                    if (--timer <= 0) {
                        timer = 0;
                        $("#wpas_payamito_awesome_support_send_otp").removeAttr('disabled');
                        OSENDOTP.value = innerhtml;
                        clearInterval(Interval);
                    }
                }, 1000);
            }
        }
    }
});