$(document).on('click', '#skill_submit', function (e) {
    e.preventDefault(); // avoid to execute the actual submit of the form.

    form = $(this).parents('#skill-add-form');

    url = '/skill/add';

    $.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {
            console.log(data);
            $('#skill-container').replaceWith(data);
        }
    });
});

