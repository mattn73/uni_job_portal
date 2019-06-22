$(function () {

    //form on route name 'company_profile', check if contact person was selected
    $('#frmCompany').on('submit', function (e) {
        e.preventDefault();
        var cPerson = $(this).find('#stlseeker').val();

        //dropdown is selected
        if (cPerson > 0) {
            $.ajax({
                type: 'POST',
                url: '/company/check',
                data: {seekerId: cPerson},
                success: function (data) {
                    if (data.statusCode == 409) {
                        alert('This user has already registered his/her company !')
                    } else {
                        //submit the form
                        document.getElementById('frmCompany').submit();
                    }
                }
            });
        } else {
            alert('Please select a contact person');
        }
    });
});