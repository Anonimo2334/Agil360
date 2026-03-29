<?php

namespace App\Services;

use App\Models\GoogleCalendarIntegration;
use App\Models\Meeting;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleServiceCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Google\Service\Calendar\EventDateTime;

class GoogleCalendarService
{
    private $client;
    
    public function __construct(GoogleCalendarIntegration $integration)
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID', ''));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET', ''));
        $this->client->setAccessType('offline');
        
        $this->client->setAccessToken($integration->access_token);
        
        // Refresh token si expira
        if ($this->client->isAccessTokenExpired() || $integration->token_expires_at->isPast()) {
            if ($integration->refresh_token) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($integration->refresh_token);

                if (!isset($newToken['error'])) {
                    $integration->update([
                        'access_token'    => $newToken['access_token'] ?? $integration->access_token,
                        'token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                }
            }
        }
    }

    public function createMeetingEvent(Meeting $meeting, array $participants = [])
    {
        try {
            $service = new GoogleServiceCalendar($this->client);
            $event = new GoogleEvent([
                'summary' => $meeting->title,
                'location' => $meeting->location ?? '',
                'description' => $meeting->description ?? '',
                'start' => [
                    'dateTime' => $this->formatDateTime($meeting->meeting_date->format('Y-m-d'), $meeting->meeting_time, 'start'),
                    'timeZone' => config('app.timezone'),
                ],
                'end' => [
                    'dateTime' => $this->formatDateTime($meeting->meeting_date->format('Y-m-d'), $meeting->meeting_time, 'end'),
                    'timeZone' => config('app.timezone'),
                ],
            ]);

            // Add attendees
            if (!empty($participants)) {
                $attendees = [];
                foreach ($participants as $p) {
                    $attendees[] = ['email' => $p->email];
                }
                $event->setAttendees($attendees);
            }

            $createdEvent = $service->events->insert('primary', $event);
            return $createdEvent->id; // El eventId de google
            
        } catch (\Exception $e) {
            \Log::error('Google Calendar Event Create Error: ' . $e->getMessage());
            return null;
        }
    }

    public function updateMeetingEvent($googleEventId, Meeting $meeting)
    {
        if (!$googleEventId) return false;

        try {
            $service = new GoogleServiceCalendar($this->client);
            $event = $service->events->get('primary', $googleEventId);
            
            $event->setSummary($meeting->title);
            $event->setLocation($meeting->location ?? '');
            $event->setDescription($meeting->description ?? '');
            
            $start = new EventDateTime();
            $start->setDateTime($this->formatDateTime($meeting->meeting_date->format('Y-m-d'), $meeting->meeting_time, 'start'));
            $start->setTimeZone(config('app.timezone'));
            $event->setStart($start);
            
            $end = new EventDateTime();
            $end->setDateTime($this->formatDateTime($meeting->meeting_date->format('Y-m-d'), $meeting->meeting_time, 'end'));
            $end->setTimeZone(config('app.timezone'));
            $event->setEnd($end);
            
            $service->events->update('primary', $googleEventId, $event);
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Calendar Event Update Error: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteMeetingEvent($googleEventId)
    {
        if (!$googleEventId) return false;

        try {
            $service = new GoogleServiceCalendar($this->client);
            $service->events->delete('primary', $googleEventId);
            return true;
        } catch (\Exception $e) {
            \Log::error('Google Calendar Event Delete Error: ' . $e->getMessage());
            return false;
        }
    }

    private function formatDateTime($date, $time, $type = 'start')
    {
        if (!$time) {
            // All day event logic or default time
            $time = $type === 'start' ? '08:00:00' : '09:00:00'; 
        } else {
            // Default sum 1 hour for 'end' if needed, but normally we just add 1 hr
            if ($type === 'end') {
                $time = \Carbon\Carbon::parse($time)->addHour()->format('H:i:s');
            }
        }
        
        $datetime = \Carbon\Carbon::parse($date . ' ' . $time);
        return $datetime->toRfc3339String();
    }
}
