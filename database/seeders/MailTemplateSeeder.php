<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mail_templates')->insert([
            [
                'act' => 'welcome_user',
                'name' => 'Welcome Email',
                'subject' => 'Welcome to Our Platform',
                'shortcodes' => json_encode([
                    'site_name' => 'Name of the site',
                    'user_name' => 'Name of the user'
                ]),
                'body' => '<h1>Welcome {{user_name}}</h1><p>Thanks for joining {{site_name}}!</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'password_reset',
                'name' => 'Password Reset',
                'subject' => 'Reset Your Password',
                'shortcodes' => json_encode([
                    'code' => 'reset code',
                    'reset_link' => 'Link to reset password',
                    'operating_system' => 'operating system of the user',
                    'browser' => 'Browser request was made from',
                    'ip' => 'User Ip address',
                    'time' => 'time of request',
                ]),
                'body' => '<div style="font-family: Montserrat, sans-serif">
                                We have received a request to reset the password for your account
                                on&nbsp;<span style="font-weight: bolder">{{time}} .<br /></span>
                            </div>
                            <div style="font-family: Montserrat, sans-serif">
                                Requested From IP:&nbsp;<span style="font-weight: bolder">{{ip}}</span
                                >&nbsp;using&nbsp;<span style="font-weight: bolder">{{browser}}</span
                                >&nbsp;on&nbsp;<span style="font-weight: bolder"
                                    >{{operating_system}}&nbsp;</span
                                >.
                            </div>
                            <div style="font-family: Montserrat, sans-serif"><br /></div>
                            <br style="font-family: Montserrat, sans-serif" />
                            <div style="font-family: Montserrat, sans-serif">
                                <div>
                                    Your account recovery code is:&nbsp;&nbsp;&nbsp;<font size="6"
                                        ><span style="font-weight: bolder">{{code}}</span></font
                                    >
                                </div>
                                <div><br /></div>
                            </div>
                            <div style="font-family: Montserrat, sans-serif"><br /></div>
                            <div style="font-family: Montserrat, sans-serif">
                                <font size="4" color="#CC0000"
                                    >If you do not wish to reset your password, please disregard this
                                    message.&nbsp;</font
                                ><br />
                            </div>
                            <div>
                            </div>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'password_reset_done',
                'name' => 'Password Reset Successful',
                'subject' => 'Your Password has been reset',
                'shortcodes' => json_encode([
                    'operating_system' => 'operating system of the user',
                    'browser' => 'Browser request was made from',
                    'ip' => 'User Ip address',
                    'time' => 'time of request',
                ]),
                'body' => '<div style="font-family: Montserrat, sans-serif">
                                You\'ve successfully reset your password
                                on&nbsp;<span style="font-weight: bolder">{{time}} .<br /></span>
                            </div>
                            <div style="font-family: Montserrat, sans-serif">
                                Requested From IP:&nbsp;<span style="font-weight: bolder">{{ip}}</span
                                >&nbsp;using&nbsp;<span style="font-weight: bolder">{{browser}}</span
                                >&nbsp;on&nbsp;<span style="font-weight: bolder"
                                    >{{operating_system}}&nbsp;</span
                                >.
                            </div>
                            <div style="font-family: Montserrat, sans-serif"><br /></div>
                            <br style="font-family: Montserrat, sans-serif" />
                            <div>
                            </div>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'email_verification',
                'name' => 'Email Verification',
                'subject' => 'Verify Your Email Address',
                'shortcodes' => json_encode([
                    'reset_link' => 'Link to reset password',
                    'code' => 'verification code'
                ]),
                'body' => '<p>your otp code is <strong>{{code}}</strong>.</p><p>We will not ask you for your OTP or PIN or password. Don\'t share your details with anyone.</p><br><p>Click <a href="{{reset_link}}">here</a> to reset your password.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'user_order_confirmation',
                'name' => 'User Order Confirmation',
                'subject' => 'Your Order Has Been Placed Successfully',
                'shortcodes' => json_encode([
                    'user_name' => 'Name of the user',
                    'order_id' => 'Unique ID of the order',
                    'order_total' => 'Total amount',
                    'site_name' => 'Name of the site',
                ]),
                'body' => '<h2>Hello {{user_name}},</h2><p>Your order <strong>#{{order_id}}</strong> has been placed with a total of <strong>{{order_total}}</strong>.</p><p>Thank you for shopping with {{site_name}}.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'seller_order_notification',
                'name' => 'New Order Notification to Seller',
                'subject' => 'You Have Received a New Order',
                'shortcodes' => json_encode([
                    'seller_name' => 'Name of the seller',
                    'order_id' => 'Order ID',
                    'buyer_name' => 'Name of the buyer',
                ]),
                'body' => '<h2>Hello {{seller_name}},</h2><p>You have received a new order (ID: {{order_id}}) from {{buyer_name}}. Please log in to your seller dashboard to process it.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'change_password',
                'name' => 'Password Change Notification',
                'subject' => 'Your Password Has Been Changed',
                'shortcodes' => json_encode([
                    'user_name' => 'Name of the user',
                    'site_name' => 'Name of the site',
                ]),
                'body' => '<p>Hello {{user_name}},</p><p>This is to confirm that your password was recently changed on {{site_name}}. If you didn\'t initiate this, please contact support immediately.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'new_message_notification',
                'name' => 'New Message Notification',
                'subject' => 'You Received a New Message',
                'shortcodes' => json_encode([
                    'user_name' => 'Name of the user',
                    'sender_name' => 'Sender of the message',
                    'message_snippet' => 'First few words of the message',
                    'message_link' => 'Link to view the message',
                ]),
                'body' => '<p>Hello {{user_name}},</p><p>You have a new message from {{sender_name}}:</p><blockquote>{{message_snippet}}</blockquote><p><a href="{{message_link}}">Click here to read</a></p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'act' => 'payment_received',
                'name' => 'Payment Confirmation',
                'subject' => 'Payment Received Successfully',
                'shortcodes' => json_encode([
                    'user_name' => 'Name of the user',
                    'amount' => 'Payment amount',
                    'payment_method' => 'Method used for payment',
                    'transaction_id' => 'Transaction ID',
                ]),
                'body' => '<p>Hello {{user_name}},</p><p>We have received your payment of <strong>{{amount}}</strong> via {{payment_method}}. Transaction ID: {{transaction_id}}.</p><p>Thank you for your purchase!</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
