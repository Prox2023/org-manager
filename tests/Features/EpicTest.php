<?php

namespace OrgManager\Tests\Features;

use OrgManager\Features\Epic;
use OrgManager\Features\Feature;
use OrgManager\Tests\TestCase;
use Brain\Monkey\Functions;

class EpicTest extends TestCase {
    private Epic $epic;

    protected function setUp(): void {
        parent::setUp();
        
        $this->epic = new class('test-epic', 'Test Epic', 'test') extends Epic {
        };
    }

    public function test_epic_constructor_sets_properties(): void {
        $this->assertEquals('test-epic', $this->epic->get_id());
        $this->assertEquals('Test Epic', $this->epic->get_name());
        $this->assertEquals('test', $this->epic->get_tag());
    }

    public function test_add_feature_adds_to_collection(): void {
        $feature = $this->createMock(Feature::class);
        $feature->method('is_enabled')->willReturn(true);
        
        $this->epic->add_feature($feature);
        
        $this->assertCount(1, $this->epic->get_features());
        $this->assertContains($feature, $this->epic->get_features());
    }

    public function test_initialize_calls_feature_methods(): void {
        $feature = $this->createMock(Feature::class);
        $feature->method('is_enabled')->willReturn(true);
        
        $feature->expects($this->once())->method('initialize');
        $feature->expects($this->once())->method('register_hooks');
        $feature->expects($this->once())->method('register_rest_routes');
        
        $this->epic->add_feature($feature);
        $this->epic->initialize();
    }

    public function test_disabled_feature_not_initialized(): void {
        $feature = $this->createMock(Feature::class);
        $feature->method('is_enabled')->willReturn(false);
        
        $feature->expects($this->never())->method('initialize');
        $feature->expects($this->never())->method('register_hooks');
        $feature->expects($this->never())->method('register_rest_routes');
        
        $this->epic->add_feature($feature);
        $this->epic->initialize();
    }
} 