<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\KalenderAkademik;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AcademicCalendarScraper
{
    private $client;
    private $cookieJar;
    private $baseUrl = 'https://mahasiswa.unair.ac.id';
    
    public function __construct()
    {
        $this->cookieJar = new CookieJar();
        $this->client = new Client([
            'cookies' => $this->cookieJar,
            'verify' => false,
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ]
        ]);
    }

    public function login($nim, $password)
    {
        try {
            // Get login page first
            $loginPageResponse = $this->client->get($this->baseUrl . '/modul/mhs/');
            $loginPageCrawler = new Crawler($loginPageResponse->getBody()->getContents());
            
            // Find CSRF token or any hidden inputs
            $hiddenInputs = [];
            $loginPageCrawler->filter('input[type="hidden"]')->each(function ($node) use (&$hiddenInputs) {
                $hiddenInputs[$node->attr('name')] = $node->attr('value');
            });

            // Attempt login
            $loginData = array_merge($hiddenInputs, [
                'nim' => $nim,
                'password' => $password,
                'submit' => 'Login'
            ]);

            $loginResponse = $this->client->post($this->baseUrl . '/modul/mhs/login.php', [
                'form_params' => $loginData
            ]);

            // Check if login successful by looking for redirect or success indicators
            $loginContent = $loginResponse->getBody()->getContents();
            
            if (strpos($loginContent, 'dashboard') !== false || 
                strpos($loginContent, 'akademik') !== false ||
                $loginResponse->getStatusCode() == 302) {
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return false;
        }
    }

    public function scrapeCalendar()
    {
        try {
            $response = $this->client->get($this->baseUrl . '/modul/mhs/#akademik!akademik-kalender.php');
            $content = $response->getBody()->getContents();
            
            $crawler = new Crawler($content);
            
            // Clear existing data
            KalenderAkademik::truncate();
            
            $crawler->filter('tbody tr')->each(function ($row, $index) {
                $cells = $row->filter('td');
                
                if ($cells->count() >= 3) {
                    $kegiatan = trim($cells->eq(0)->text());
                    $tanggalMulai = $this->parseDate(trim($cells->eq(1)->text()));
                    $tanggalSelesai = $this->parseDate(trim($cells->eq(2)->text()));
                    
                    // Determine class type from tr class
                    $classType = 'odd';
                    if ($row->attr('class')) {
                        $classType = strpos($row->attr('class'), 'even') !== false ? 'even' : 'odd';
                    }
                    
                    if ($tanggalMulai && $tanggalSelesai) {
                        KalenderAkademik::create([
                            'kegiatan' => $kegiatan,
                            'tanggal_mulai' => $tanggalMulai,
                            'tanggal_selesai' => $tanggalSelesai,
                            'class_type' => $classType
                        ]);
                    }
                }
            });
            
            return true;
        } catch (\Exception $e) {
            Log::error('Scraping error: ' . $e->getMessage());
            return false;
        }
    }

    private function parseDate($dateString)
    {
        try {
            // Format: "03 FEB 2025"
            $months = [
                'JAN' => '01', 'FEB' => '02', 'MAR' => '03', 'APR' => '04',
                'MAY' => '05', 'JUN' => '06', 'JUL' => '07', 'AUG' => '08',
                'SEP' => '09', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'
            ];
            
            $parts = explode(' ', $dateString);
            if (count($parts) === 3) {
                $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                $month = $months[$parts[1]] ?? '01';
                $year = $parts[2];
                
                return Carbon::createFromFormat('Y-m-d', "$year-$month-$day");
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}