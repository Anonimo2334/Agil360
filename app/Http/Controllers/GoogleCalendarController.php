<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GoogleCalendarIntegration;
use Google\Client as GoogleClient;

class GoogleCalendarController extends Controller
{
    private function getGoogleClient()
    {
        $client = new GoogleClient();
        $client->setClientId(env('GOOGLE_CLIENT_ID', ''));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET', ''));
        $client->setRedirectUri(route('google.calendar.callback'));
        $client->addScope(\Google\Service\Calendar::CALENDAR);
        $client->addScope(\Google\Service\Oauth2::USERINFO_EMAIL);
        $client->addScope(\Google\Service\Oauth2::USERINFO_PROFILE);
        $client->setAccessType('offline');
        $client->setPrompt('consent'); // Para forzar que devuelva un refresh token si ya autorizó antes
        
        return $client;
    }

    public function connect()
    {
        $client = $this->getGoogleClient();
        $authUrl = $client->createAuthUrl();

        return redirect()->away($authUrl);
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('profile.index', ['tab' => 'settings'])->with('error', 'Autorización de Google Calendar cancelada o fallida.');
        }

        $client = $this->getGoogleClient();
        
        try {
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($token['error'])) {
                return redirect()->route('profile.index', ['tab' => 'settings'])->with('error', 'Error al autenticar: ' . $token['error']);
            }

            // Get user info to fetch the specific email that was connected
            $oauth2 = new \Google\Service\Oauth2($client);
            $googleUser = $oauth2->userinfo->get();

            $user = auth()->user();

            $integration = GoogleCalendarIntegration::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'google_id' => $googleUser->id,
                    'email' => $googleUser->email,
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null, // Refresh token is only sent on first auth or when consent prompted
                    'token_expires_at' => now()->addSeconds($token['expires_in']),
                ]
            );

            // Si Google no devuelve refresh token pero ya lo teníamos de antes y estamos actualizando,
            // no lo perdemos porque updateOrCreate solo sobreescribe lo de arriba. Espera, updateOrCreate pisa los datos.
            // Asi que necesitamos una lógica mejor
            if (empty($token['refresh_token'])) {
                $existing = GoogleCalendarIntegration::where('user_id', $user->id)->first();
                if ($existing && $existing->refresh_token) {
                    $integration->refresh_token = $existing->refresh_token;
                    $integration->save();
                }
            }

            return redirect()->route('profile.index', ['tab' => 'settings'])
                ->with('success', 'Google Calendar conectado exitosamente ('.$googleUser->email.').');

        } catch (\Exception $e) {
            return redirect()->route('profile.index', ['tab' => 'settings'])->with('error', 'Excepción: ' . $e->getMessage());
        }
    }

    public function disconnect()
    {
        $user = auth()->user();
        GoogleCalendarIntegration::where('user_id', $user->id)->delete();

        return redirect()->route('profile.index', ['tab' => 'settings'])->with('success', 'Calendario desconectado correctamente.');
    }
}
