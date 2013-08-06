//create crossdomain socket connection


$(document).ready(function () {
    $('.ipModuleDesign .ipaOpenMarket').on('click', ipDesignOpenMarket);
    $('.ipModuleDesign .ipaOpenOptions').on('click', ipDesignOpenOptions);

    $('.ipsInstallTheme').on('click', function (e) {
        e.preventDefault();

        console.log('install theme');

        $.ajax({
            url: ip.baseUrl,
            dataType: 'json',
            type: 'POST',
            data: {'g': 'standard', 'm': 'design', 'ba': 'installTheme', 'themeName': $(this).data('theme'), 'securityToken': ip.securityToken},
            success: function (response) {
                console.log('response: ', response);
                if (response.status && response.status == 'success') {
                    window.location = ip.baseUrl + '?g=standard&m=design&ba=index';
                } else if (response.error) {
                    alert(response.error);
                }
            },
            error: function () {
                alert('Error installing theme.');
            }
        });


    });

});

