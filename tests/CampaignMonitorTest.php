<?php

namespace CampaignMonitorMailer\Tests;

use CampaignMonitorMailer\Mail\CampaignMonitor;
use CampaignMonitorMailer\Templates\CampaignMonitorTemplateManager;
use CampaignMonitorMailer\Exceptions\CampaignMonitorException;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Mail;

class CampaignMonitorTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \CampaignMonitorMailer\Providers\CampaignMonitorMailerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('campaign-monitor.config.apiKey', 'test-api-key');
        $app['config']->set('campaign-monitor.templates.core.email_verification', 'test-template-id');
    }

    public function test_campaign_monitor_mailable_creation()
    {
        $mailable = new CampaignMonitor('test-template-id', ['name' => 'John Doe']);

        $this->assertEquals('test-template-id', $mailable->getSmartEmailId());
        $this->assertEquals(['name' => 'John Doe'], $mailable->getData());
    }

    public function test_template_manager_get_template()
    {
        $templateId = CampaignMonitorTemplateManager::get('core', 'email_verification');

        $this->assertEquals('test-template-id', $templateId);
    }

    public function test_template_manager_throws_exception_for_missing_template()
    {
        $this->expectException(\InvalidArgumentException::class);

        CampaignMonitorTemplateManager::get('core', 'non_existent_template');
    }
}
