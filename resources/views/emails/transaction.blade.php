<!DOCTYPE html>
<html>

    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <style type="text/css" rel="stylesheet" media="all">
            /* Media Queries */
            @media  only screen and (max-width: 500px) {
                .button {
                    width: 100% !important;
                }
            }
            body{font-family:sans-serif;color: #74787E;}
        </style>
    </head>



    <body style="margin: 0; padding: 0; width: 100%; background-color: #F2F4F6;">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 100%; margin: 0; padding: 0; background-color: #F2F4F6;" align="center">
                    <table width="100%" cellpadding="0" cellspacing="0">
                        <!-- Logo -->
                        <tr>
                            <td style="padding: 25px 0; text-align: center;">
                                <a style="font-size: 16px; font-weight: bold; color: #2F3133; text-decoration: none; text-shadow: 0 1px 0 white;" href="http://localhost:8000" target="_blank">
                                  {{ config('app.name') }}
                                </a>
                            </td>
                        </tr>

                        <!-- Email Body -->
                        <tr>
                            <td style="width: 100%; margin: 0; padding: 0; border-top: 1px solid #EDEFF2; border-bottom: 1px solid #EDEFF2; background-color: #FFF;" width="100%">
                                <table style="width: auto; max-width: 570px; margin: 0 auto; padding: 0;" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 35px;">
                                         {{ $content }}
                                            <!-- Action Button -->
                                            <table style="width: 100%; margin: 30px auto; padding: 0; text-align: center;" align="center" width="100%" cellpadding="0" cellspacing="0">
                                                <tr>
                                                    <td align="center">
                                                     
                                                    </td>
                                                </tr>
                                            </table>

                                            <!-- Outro -->

                                            <!-- Salutation -->
                                            <p style="margin-top: 0; color: #74787E; font-size: 16px; line-height: 1.5em;">
                                                Regards,<br>{{ config('app.name') }}
                                            </p>

                                            <!-- Sub Copy -->
                                            <table style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #EDEFF2;">
                                                <tr>
                                                    <td>
                                                        <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                                            If you’re having trouble clicking the "Application Link" button,
                                                            copy and paste the URL below into your web browser:
                                                        </p>

                                                        <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                                            <a style="color: #3869D4;" href="http://localhost:8000/admin" target="_blank">
                                                                {{ url('admin') }}
                                                            </a>
                                                        </p>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td>
                                <table style="width: auto; max-width: 570px; margin: 0 auto; padding: 0; text-align: center;" align="center" width="570" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="color: #AEAEAE; padding: 35px; text-align: center;">
                                            <p style="margin-top: 0; color: #74787E; font-size: 12px; line-height: 1.5em;">
                                                &copy; 2018
                                                <a style="color: #3869D4;" href="{{ url('admin') }}" target="_blank">{{ config('app.name') }}</a>.
                                                All rights reserved.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
