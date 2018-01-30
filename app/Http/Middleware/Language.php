<?php namespace App\Http\Middleware;

use Closure;
use Illuminate\Routing\Redirector;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Routing\Middleware;
use Session;

class Language implements Middleware {

    public function __construct(Application $app, Redirector $redirector, Request $request) {
        $this->app = $app;
        $this->redirector = $redirector;
        $this->request = $request;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Make sure current locale exists.
        session_start();
        if(isset($_GET['language']))
        {
			$locale = isset($_GET['language']) ? $_GET['language'] : 'en';
		}
		else
		{
			$locale = isset($_SESSION['language']) ? $_SESSION['language'] : 'en';
		}
		if ( ! array_key_exists($locale, $this->app->config->get('app.locales'))) {
            $segments = $request->segments();
            $segments[0] = $this->app->config->get('app.fallback_locale');

            return $this->redirector->to(implode('/', $segments));
        }

        $this->app->setLocale($locale);

        return $next($request);
    }

}
