<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <style>
        /* This styles you should add to your html as inline-styles */
        /* You can easily do it with http://inlinestyler.torchboxapps.com/ */
        /* Copy this html-window code converter and click convert button */
        /* After that you can remove this style from your code */

        body {
            margin: 0;
            padding: 0;
            mso-line-height-rule: exactly;
            min-width: 100%;
        }

        .wrapper {
            display: table;
            table-layout: fixed;
            width: 100%;
            min-width: 620px;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        body,
        .wrapper {
            background-color: #eeeeee;
        }

        /* Basic */
        table {
            border-collapse: collapse;
            border-spacing: 0;
        }

        table.center {
            margin: 0 auto;
            width: 602px;
        }

        td {
            padding: 0;
            vertical-align: top;
        }

        .spacer,
        .border {
            font-size: 1px;
            line-height: 1px;
        }

        .spacer {
            width: 100%;
            line-height: 16px
        }

        .border {
            background-color: #e0e0e0;
            width: 1px;
        }

        .padded {
            padding: 0 24px;
        }

        img {
            border: 0;
            -ms-interpolation-mode: bicubic;
        }

        .image {
            font-size: 12px;
        }

        .image img {
            display: block;
        }

        strong,
        .strong {
            font-weight: 700;
        }

        h1,
        h2,
        h3,
        p,
        ol,
        ul,
        li {
            margin-top: 0;
        }

        ol,
        ul,
        li {
            padding-left: 0;
        }

        a {
            text-decoration: none;
            color: #616161;
        }

        .btn {
            background-color: #2196F3;
            border: 1px solid #2196F3;
            border-radius: 2px;
            color: #ffffff;
            display: inline-block;
            font-size: 14px;
            font-weight: 400;
            line-height: 36px;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
            width: 200px;
            height: 36px;
            padding: 0 8px;
            margin: 0;
            outline: 0;
            outline-offset: 0;
            -webkit-text-size-adjust: none;
            mso-hide: all;
        }

        /* Top panel */
        .title {
            text-align: left;
        }

        .subject {
            text-align: right;
        }

        .title,
        .subject {
            width: 300px;
            padding: 8px 0;
            color: #616161;
            font-weight: 400;
            font-size: 12px;
            line-height: 14px;
        }

        /* Header */
        .logo {
            padding: 16px 0;
        }

        /* Logo */
        .logo-image {}

        /* Main */
        .main {
            -webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);
            -moz-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);
        }

        /* Content */
        .columns {
            margin: 0 auto;
            width: 600px;
            background-color: #ffffff;
            font-size: 14px;
        }

        .column {
            text-align: left;
            background-color: #ffffff;
            font-size: 14px;
        }

        .column-top {
            font-size: 24px;
            line-height: 24px;
        }

        .content {
            width: 100%;
        }

        .column-bottom {
            font-size: 8px;
            line-height: 8px;
        }

        .content h1 {
            margin-top: 0;
            margin-bottom: 16px;
            color: #212121;
            font-weight: 400;
            font-size: 20px;
            line-height: 28px;
        }

        .content p {
            margin-top: 0;
            margin-bottom: 16px;
            color: #212121;
            font-weight: 400;
            font-size: 16px;
            line-height: 24px;
        }

        .content .caption {
            color: #616161;
            font-size: 12px;
            line-height: 20px;
        }

        /* Footer */
        .signature,
        .subscription {
            vertical-align: bottom;
            width: 300px;
            padding-top: 8px;
            margin-bottom: 16px;
        }

        .signature {
            text-align: left;
        }

        .subscription {
            text-align: right;
        }

        .signature p,
        .subscription p {
            margin-top: 0;
            margin-bottom: 8px;
            color: #616161;
            font-weight: 400;
            font-size: 12px;
            line-height: 18px;
        }

    </style>

    <center class="wrapper">
        <table class="top-panel center" width="602" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td class="title" width="300">Daafy</td>
                    <td class="subject" width="300"><a class="strong" href="#" target="_blank">www.DaafyApp.com</a></td>
                </tr>
                <tr>
                    <td class="border" colspan="2">&nbsp;</td>
                </tr>
            </tbody>
        </table>

        <div class="spacer">&nbsp;</div>

        <table class="main center" width="602" border="0" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td class="column">
                        <div class="column-top">&nbsp;</div>
                        <table class="content" border="0" cellspacing="0" cellpadding="0">
                            <tbody>
                                <tr>
                                    <td class="padded">
                                        <h3>Dear {{$name}}</h3>
                                        <p>We have sent you this email in response to your request to reset your
                                            password on company
                                            name.
                                            To reset your password, use the code bellow:</p>
                                        <div style="font-size: 26px; line-height: 21.6px; text-align:center; margin-bottom: 20px;"
                                            class="mt-8 text-sm">
                                            {{$code}} </div>
                                        <!-- <p style="text-align:center;"><a href="" class="btn">Verify Email Address</a></p> -->
                                        <p style="text-align:center;">
                                            <em>Please ignore this email if you did not request a password change.</em>
                                        </p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="column-bottom">&nbsp;</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="spacer">&nbsp;</div>
    </center>
</body>

</html>
