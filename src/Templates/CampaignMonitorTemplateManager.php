<?php

namespace CampaignMonitorMailer\Templates;

class CampaignMonitorTemplateManager
{
    /**
     * Get a template ID by category and name
     */
    public static function get(string ...$path): string
    {
        $configPath = 'campaign-monitor.templates.' . implode('.', $path);
        $template = config($configPath);

        if (! $template) {
            $pathString = implode('.', $path);
            throw new \InvalidArgumentException("Template not found: {$pathString}");
        }

        return $template;
    }
}
