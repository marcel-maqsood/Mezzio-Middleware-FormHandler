$(document).ready(function () 
{

    $('#submit').on("click", function () 
    {
        // aId is equal to your Form's ID, you can set it to whatever you like.
        var yourFormJson = generateFormJSON($("#aId"));
        $.ajax(
            {
                type: 'POST',
                url: window.location.href, // Set the URL on which your Middleware will receive its data.
                data: yourFormJson,
                contentType: 'application/json;charset=UTF-8', // This is needed and shouldn't be touched.
                xhrFields:
                {
                    // You may not need this flag, but it might be mandatory if your application uses authentication.
                    withCredentials: true
                },
                success: function (response) 
                {
                    // Implement logic for successful response.
                },
                error: function (error) 
                {
                    //Implement your ErrorHandling.
                }
            });

    });

    function generateFormJSON(form) 
    {
        var formData =
        {
            data: {}
        };

        form.serializeArray().forEach(function (item) 
        {
            if (item.name.startsWith("data[")) 
            {
                var key = item.name.match(/\[([^\]]+)\]/)[1];
                formData.data[key] = item.value;
            }
            else 
            {
                formData[item.name] = item.value;
            }
        });

        return JSON.stringify(formData);
    }
});