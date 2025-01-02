<?php

namespace OrgManager\Tests\Features;

use OrgManager\Features\Feature;
use OrgManager\Tests\TestCase;

class FeatureTest extends TestCase {
    private Feature $feature;

    protected function setUp(): void {
        parent::setUp();
        
        $this->feature = new class extends Feature {
            protected string $id = 'test-feature';
            protected string $name = 'Test Feature';
            protected string $description = 'Test Description';
            protected array $tags = ['test', 'feature'];
            protected bool $enabled = true;
            
            public function initialize(): void {}
            public function register_hooks(): void {}
            public function register_rest_routes(): void {}
        };
    }

    public function test_feature_properties_accessible(): void {
        $this->assertEquals('test-feature', $this->feature->get_id());
        $this->assertEquals('Test Feature', $this->feature->get_name());
        $this->assertEquals('Test Description', $this->feature->get_description());
        $this->assertEquals(['test', 'feature'], $this->feature->get_tags());
        $this->assertTrue($this->feature->is_enabled());
    }

    public function test_disabled_feature(): void {
        $feature = new class extends Feature {
            protected string $id = 'test';
            protected string $name = 'Test';
            protected string $description = 'Test';
            protected array $tags = [];
            protected bool $enabled = false;
            
            public function initialize(): void {}
            public function register_hooks(): void {}
            public function register_rest_routes(): void {}
        };

        $this->assertFalse($feature->is_enabled());
    }
} 