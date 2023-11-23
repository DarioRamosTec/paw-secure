<div style="width:100%;background:#ffffff;text-align:left">
    <div style="margin:auto; max-width: 700px; padding-top: 30px; padding-bottom: 30px; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">
        <div style="width: 100%; background-color: rgb(243, 247, 252); height: 60px; border-bottom: 1px solid #dee2e6;">
            <table style="width: 100%; height: 100%;">
                <tbody>
                    <tr>
                        <td style="text-align: center;"><img src="{{ $message->embed($pathToImage) }}" alt="" role="presentation" style="height: 40px; background-color: rgb(243, 247, 252);"></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div style="background-color: rgb(249, 252, 254); padding: 25px; font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">
            <h1 style="font-weight: 300; text-align: center; margin-top: 2px; margin-bottom: 6px;">¡Hello, <span style="font-style: italic; color: #4DBBC8;">{{ $userName }}</span>!</h1>
            <p style="font-weight: 300; font-size: 1.1em;">¡Thanks to join our team!</p>
            <p style="font-weight: 300; font-size: 1.1em;">Please, to activate your account use {{ $activateUrl }}.</p>
            <p style="font-weight: 300;">We hope that you will continue your path in this awesome story.</p>
            <small><span style="font-style: italic; font-weight: 300;"> - Gregorio Samsa</span></small>
        </div>
        <hr style="opacity: 0.5;">
        <span style="font-style: italic; font-size: small; font-weight: 300; text-align: center; width: 100%;">Gregorio Samsa, México</span>
    </div>
</div>
