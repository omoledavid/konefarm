<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            $emailTemplate = <<<HTML
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                      <meta charset="UTF-8">
                      <title>Email</title>
                      <style>
                        body {
                          margin: 0;
                          padding: 0;
                          background-color: #37474f;
                          font-family: 'Segoe UI', sans-serif;
                        }

                        .email-container {
                          max-width: 600px;
                          margin: 40px auto;
                          background-color: #ffffff;
                          border-radius: 8px;
                          overflow: hidden;
                          box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                        }

                        .email-header {
                          background-color: #0288d1;
                          padding: 20px;
                          text-align: center;
                        }

                        .email-header img {
                          max-height: 50px;
                        }

                        .email-body {
                          padding: 40px 30px;
                        }

                        .email-body h2 {
                          margin-top: 0;
                          text-align: center;
                          color: #37474f;
                        }

                        .divider {
                          width: 40px;
                          height: 2px;
                          background-color: #0288d1;
                          margin: 10px auto 30px auto;
                        }

                        .message {
                          color: #757575;
                          font-size: 16px;
                          line-height: 1.5;
                        }

                        .email-footer {
                          background-color: #f5f5f5;
                          text-align: center;
                          padding: 15px 10px;
                          font-size: 13px;
                          color: #9e9e9e;
                        }

                        .email-footer a {
                          color: #0288d1;
                          text-decoration: none;
                        }
                      </style>
                    </head>
                    <body>
                      <div class="email-container">
                        <div class="email-header">
                          <img src="{{logo}}" alt="{{site_name}} Logo">
                        </div>
                        <div class="email-body">
                          <h2>Hello {{fullname}}</h2>
                          <div class="divider"></div>
                          <div class="message">
                            {{message}}
                          </div>
                        </div>
                        <div class="email-footer">
                          © 2024 <a href="#">{{site_name}}</a>. All Rights Reserved.
                        </div>
                      </div>
                    </body>
                    </html>
                    HTML;

            DB::table('general_settings')->updateOrInsert(
                ['id' => 1],
                [
                    'site_name' => 'Kone Farm',
                    'email_from' => 'info@konefarm.com',
                    'email_template' => $emailTemplate,
                    'mail_config' => null,
                    'global_shortcodes' => json_encode([
                        'site_name' => 'Name of your site',
                        'currency_symbol' => 'Symbol of currency'
                    ]),
                    'ev' => 0,
                    'auto_approve' => false,
                    'register_status' => true,
                    'deposit_status' => false,
                    'withdraw_status' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
