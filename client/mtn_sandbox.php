<!DOCTYPE html>
<html>

<head>
    <title>JSSample</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
</head>

<body>

    <script type="text/javascript">
        $(function() {
            var params = {
                // Request parameters
            };

            $.ajax({
                    url: "https://sandbox.momodeveloper.mtn.com/v1_0/apiuser",
                    beforeSend: function(xhrObj) {
                        // Request headers
                        xhrObj.setRequestHeader("Access-Control-Allow-Origin:", "*");
                        xhrObj.setRequestHeader("X-Reference-Id", "");
                        xhrObj.setRequestHeader("Content-Type", "application/json");
                        xhrObj.setRequestHeader("Ocp-Apim-Subscription-Key", "81b77f3c482e4831af0e85427a01a38c");
                    },
                    type: "GET",
                    // Request body
                    data:`${params}`,
                })
                .done(function(data) {
                    alert("success");
                    console.log(data);
                })
                .fail(function(error) {
                    alert(error.message);
                    console.log(error);
                });
        });
    </script>
</body>

</html>