<?php

namespace App\Providers;

use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;




class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //

        if(Schema::HasTable('smtp_settings')){

            $smtp_setting = SmtpSetting::first();

            if($smtp_setting){

                $data = [

                    'driver' => $smtp_setting->mailer,
                    'host' => $smtp_setting->host,
                    'port' => $smtp_setting->port,
                    'username' => $smtp_setting->username,
                    'password' => $smtp_setting->password,
                    'encryption' => $smtp_setting->encryption,
                    'from_address' => $smtp_setting->from_address,
                    'from_name' => 'Easy Hotel',
                ];
                Config::set('Mail',$data);
            }
        }
    }
}
