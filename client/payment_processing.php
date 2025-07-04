<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, Sans;
            margin: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300px;
            text-align: center;
            transform: translateX(-50%);
        }

        .spanner {
            position: absolute;
            top: 50%;
            left: 0;
            background: #2a2a2a;
            width: 100%;
            display: block;
            text-align: center;
            height: 300px;
            color: #FFF;
            transform: translateY(-50%);
            z-index: 1000;
            visibility: hidden;
        }

        .overlay {
            position: fixed;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            visibility: hidden;
        }

        .loader,
        .loader:before,
        .loader:after {
            border-radius: 50%;
            width: 2.5em;
            height: 2.5em;
            -webkit-animation-fill-mode: both;
            animation-fill-mode: both;
            -webkit-animation: load7 1.8s infinite ease-in-out;
            animation: load7 1.8s infinite ease-in-out;
        }

        .loader {
            color: #ffffff;
            font-size: 10px;
            margin: 80px auto;
            position: relative;
            text-indent: -9999em;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-animation-delay: -0.16s;
            animation-delay: -0.16s;
        }

        .loader:before,
        .loader:after {
            content: '';
            position: absolute;
            top: 0;
        }

        .loader:before {
            left: -3.5em;
            -webkit-animation-delay: -0.32s;
            animation-delay: -0.32s;
        }

        .loader:after {
            left: 3.5em;
        }

        @-webkit-keyframes load7 {

            0%,
            80%,
            100% {
                box-shadow: 0 2.5em 0 -1.3em;
            }

            40% {
                box-shadow: 0 2.5em 0 0;
            }
        }

        @keyframes load7 {

            0%,
            80%,
            100% {
                box-shadow: 0 2.5em 0 -1.3em;
            }

            40% {
                box-shadow: 0 2.5em 0 0;
            }
        }

        .show {
            visibility: visible;
        }

        .spanner,
        .overlay {
            opacity: 0;
            -webkit-transition: all 0.3s;
            -moz-transition: all 0.3s;
            transition: all 0.3s;
        }

        .spanner.show,
        .overlay.show {
            opacity: 1
        }
    </style>
</head>

<body>

    <!--<div class="wrapper">-->
    <!--<input type="submit"/>-->
    <!--</div>-->
    <div class="overlay show"></div>
    <div class="spanner show">
        <div class="loader"></div>
        <p>Processing your transaction, please wait.</p>
    </div>
    <script>
        //     $("input[type='submit']").click(function(){
        //   $("div.spanner").addClass("show");
        //   $("div.overlay").addClass("show");
        // });
    </script>
</body>

</html>