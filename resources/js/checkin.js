import { Html5Qrcode } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', () => {
    const readerEl = document.getElementById('qr-reader');
    if (!readerEl) return;

    const scanner = new Html5Qrcode('qr-reader');
    const baseCheckinUrl = readerEl.dataset.checkinBaseUrl;

    scanner
        .start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                scanner.stop().finally(() => {
                    const token = decodedText.split('/').pop();
                    window.location.href = `${baseCheckinUrl}/${token}`;
                });
            },
            () => {}
        )
        .catch((err) => {
            document.getElementById('qr-error').textContent =
                'Could not access camera: ' + err;
        });
});
