<?php

namespace App\Providers;


use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;



class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);

        //Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        //Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        //Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // 会員登録画面
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // メール認証画面のビューを指定
        Fortify::verifyEmailView(function () {
            return view('auth.verify-email');
        });

        Log::info('verifyEmailView: 成功');

        // メール認証画面のリダイレクト
        Fortify::redirects('email-verification', '/mypage/profile');  // メール認証後のリダイレクト先
        Log::info('email-verification: 成功');

    
        Fortify::authenticateThrough(function (Request $request) {

            return array_filter([
                // ログイン時のレート制限チェック
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,

                // カスタムバリデーションアクション
                \App\Actions\ValidateCustomLoginRequest::class,

                // 認証の試行
                AttemptToAuthenticate::class,

                // 認証成功時のセッション準備
                PrepareAuthenticatedSession::class,
            ]);
        });

        // ログイン画面
        Fortify::loginView(function () {
            return view('auth.login');
        });

        
        Fortify::redirects('failed-login', '/login'); // ログイン失敗時のリダイレクト先をURLで明示的に指定

        // ログイン時のレートリミット
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
