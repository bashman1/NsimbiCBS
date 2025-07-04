  <!DOCTYPE html>
  <html>

  <head>

  </head>

  <body>
      <!-- <video id="video" playsinline autoplay></video> -->
      <button id="snap">Capture</button>
      <canvas id="canvas" width="640" height="480"></canvas>

      <span id="spanErrorMsg"></span>
  </body>

  </html>

  <script>
      'use strict';
    //   const video = document.getElementById('video');
      const canvas = document.getElementById('canvas');
      const snap = document.getElementById('snap');
      const errorMsgElement = document.getElementById('spanErrorMsg');


      const constraints = {
          audio: true,
          video: {
              width: 1280,
              height: 720
          }
      };


      // access webcam
      async function init() {
          try {
              const stream = await navigator.mediaDevices.getUserMedia(constraints);
              handleSuccess(stream);
          } catch (e) {
              errorMsgElement.innerHTML = `navigator.getUserMedia.error:${e.toString()}`;
          }
      }

      // success
      function handleSuccess(stream) {
          window.stream = stream;
          video.srcObject = stream;
      }

      // load init
      init();

      // draw image
      var context = canvas.getContext('2d');
      snap.addEventListener("click", function() {
          context.drawImage(video, 0, 0, 640, 480);

      });
  </script>