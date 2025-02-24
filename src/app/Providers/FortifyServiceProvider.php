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

        Fortify::redirects('register', function () {
            $user = Auth::user(); // Auth::check() ではなく、Auth::user() を使用

            if ($user && is_null(Auth::user()->email_verified_at)) {
                return '/email/verify';  // メール認証が未完了の場合、メール認証画面へ
            }

            return '/mypage/profile';  // メール認証済みの場合、プロフィール画面へ
        });



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

        Fortify::redirects('login', function () {
            $user = Auth::user();

            if (is_null($user)) {
                return '/'; // 未ログインの場合はトップページへ（または適切なページ）
            }

            if (is_null($user->email_verified_at)) {
                return '/email/verify';  // メール未認証の場合
            }

            return '/'; // メール認証済みの場合
        });

        // メール認証画面のリダイレクト
        Fortify::redirects('email-verification', '/mypage/profile');  // メール認証後のリダイレクト先

        Fortify::redirects('failed-login', '/login'); // ログイン失敗時のリダイレクト先をURLで明示的に指定

        // ログイン時のレートリミット
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
