<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocaleFromHeader Middleware
 * 
 * Automatically detects and sets the application locale based on the
 * Accept-Language header from client requests. Supports English (en)
 * and Indonesian (id) languages.
 * 
 * Features:
 * - Automatic language detection from Accept-Language header
 * - Fallback to default locale if unsupported language is requested
 * - Support for language priority (q-values)
 * - Clean implementation with proper error handling
 * 
 * @package App\Http\Middleware
 * @author LMS Development Team
 * @version 1.0.0
 */
class SetLocaleFromHeader
{
    /**
     * Supported locales for the application
     * 
     * @var array
     */
    protected array $supportedLocales = ['en', 'id'];

    /**
     * Default locale when no supported locale is found
     * 
     * @var string
     */
    protected string $defaultLocale = 'en';

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        
        // Determine the best locale to use
        $locale = $this->determineLocale($acceptLanguage);
        
        // Set the application locale
        App::setLocale($locale);
        
        // Add locale information to request for debugging
        $request->attributes->set('detected_locale', $locale);
        $request->attributes->set('original_accept_language', $acceptLanguage);
        
        return $next($request);
    }

    /**
     * Determine the best locale based on Accept-Language header
     * 
     * @param string|null $acceptLanguage
     * @return string
     */
    protected function determineLocale(?string $acceptLanguage): string
    {
        // If no Accept-Language header, use default
        if (empty($acceptLanguage)) {
            return $this->defaultLocale;
        }

        // Parse the Accept-Language header
        $preferredLanguages = $this->parseAcceptLanguage($acceptLanguage);
        
        // Find the first supported language
        foreach ($preferredLanguages as $language => $priority) {
            // Check exact match first (e.g., 'en', 'id')
            if (in_array($language, $this->supportedLocales)) {
                return $language;
            }
            
            // Check language prefix (e.g., 'en-US' -> 'en')
            $languagePrefix = substr($language, 0, 2);
            if (in_array($languagePrefix, $this->supportedLocales)) {
                return $languagePrefix;
            }
        }
        
        // No supported language found, use default
        return $this->defaultLocale;
    }

    /**
     * Parse Accept-Language header into array of languages with priorities
     * 
     * @param string $acceptLanguage
     * @return array
     */
    protected function parseAcceptLanguage(string $acceptLanguage): array
    {
        $languages = [];
        
        // Split by comma to get individual language entries
        $entries = explode(',', $acceptLanguage);
        
        foreach ($entries as $entry) {
            $entry = trim($entry);
            
            // Check if entry has quality value (q=0.8)
            if (strpos($entry, ';q=') !== false) {
                [$language, $quality] = explode(';q=', $entry, 2);
                $language = trim($language);
                $priority = (float) trim($quality);
            } else {
                $language = $entry;
                $priority = 1.0; // Default priority
            }
            
            // Clean up language code
            $language = strtolower($language);
            
            // Store with priority
            $languages[$language] = $priority;
        }
        
        // Sort by priority (highest first)
        arsort($languages);
        
        return $languages;
    }

    /**
     * Get the list of supported locales
     * 
     * @return array
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * Get the default locale
     * 
     * @return string
     */
    public function getDefaultLocale(): string
    {
        return $this->defaultLocale;
    }

    /**
     * Check if a locale is supported
     * 
     * @param string $locale
     * @return bool
     */
    public function isLocaleSupported(string $locale): bool
    {
        return in_array($locale, $this->supportedLocales);
    }
}