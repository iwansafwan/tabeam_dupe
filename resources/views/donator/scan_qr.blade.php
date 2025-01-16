<x-app-layout>

    <style>
        .display_box {
            width: 100% !important;
            border: 1px solid black;
            padding: .5rem .75rem !important;
            border-radius: 15px !important;
        }

        .display_box_1 {
            width: 100% !important;
            border: 1px solid black;
            padding: .5rem .75rem !important;
            height: 100px !important;
            border-radius: 15px !important;
        }

        .display_box_2 {
            width: 100% !important;
            padding: 10px 0px !important;
        }

        #reader {
            margin: auto;
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
    </style>

    <div class="row">
        <div class="col-md-12 col-12 p-3">
            <span class="title_header">Scan QR Fund</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mx-0 my-2">
                        <div class="col-md-12 col-12 d-flex justify-content-center">
                            <div id="reader" style="width: 400px; display: none;"></div>
                            <div id="qr-result" style="margin-top: 10px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>

<script>
    $(document).ready(function() {

        if (typeof Html5Qrcode !== 'undefined') {

            console.log('Html5QrCode loaded successfully');

        } else {

            console.log('Html5QrCode not loaded');

        }

        const readerDiv = $("#reader");
        // show qr code reader
        readerDiv.show();

        // bind qr code scanner with DOM id='reader'
        // start qr code scanner
        const html5QrCode = new Html5Qrcode("reader");

        html5QrCode.start(
            { facingMode: "environment" }, // Use the back camera
            {
                fps: 10, // Frames per second
                qrbox: { width: 250, height: 250 } // Scanning box size
            },
            function (decodedText, decodedResult) {
                // Success callback when a QR code is scanned
                console.log(`QR Code scanned: ${decodedText}`);

                // Redirect to the URL encoded in the QR code
                window.location.href = decodedText; // Redirect to the decoded URL

                html5QrCode.stop(); // Stop scanning after a successful scan
                readerDiv.hide();
            },
            function (errorMessage) {
                // Error callback
                console.warn(`QR Code scan error: ${errorMessage}`);
            }
        ).catch(function (err) {
            console.error(`Unable to start scanning: ${err}`);
        });
    });
</script>

{{-- const html5QrCode = new Html5QrCode("reader");

        html5QrCode.start({
                // use back camera
                facingmMode: "environment"
            }, {
                // frame per second
                fps: 10,
                // scanning box size
                qrbox: {
                    width: 250,
                    height: 250
                }
            },

            function(decodedText, decodedResult) {
                // success callback when a QR code is scanned
                console.log(`QR Code scanned: ${decodedText}`);
                // Redirect to the URL encoded in the QR Code
                window.location.href = decodedText;
                // stop scanning after successful scan
                html5QrCode.stop();
                readerDiv.hide();
            },
            function(errorMessage) {
                // error callback
                console.warn(`QR Code scan error: ${errorMessage}`);
            }
        ).catch(function(err) {
            console.error(`Unable to start scanning: ${err}`);
        }); --}}