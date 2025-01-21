<?php

return [
    'packages' => [
        'commons' => [
            'name' => 'Commons',
            'description' => 'Core functionalities and common utilities',
            'branch' => 'master',
            'package' => 'nextdeveloper/commons',
            'publishable' => [
                'config' => true,
                'migrations' => false,
            ],
            'provider' => 'NextDeveloper\Commons\CommonsServiceProvider',
            'dependencies' => [],
        ],
        'iam' => [
            'name' => 'IAM',
            'description' => 'Identity and Access Management',
            'branch' => 'master',
            'package' => 'nextdeveloper/iam',
            'publishable' => [
                'config' => true,
                'migrations' => true,
            ],
            'provider' => 'NextDeveloper\IAM\IAMServiceProvider',
            'dependencies' => ['commons'],
        ],
        'crm' => [
            'name' => 'CRM',
            'description' => 'Customer Relationship Management',
            'branch' => 'master',
            'package' => 'nextdeveloper/crm',
            'publishable' => [
                'config' => true,
                'migrations' => true,
            ],
            'provider' => 'NextDeveloper\CRM\CRMServiceProvider',
            'dependencies' => ['commons'],
        ],
        'agenda' => [
            'name' => 'Agenda',
            'description' => 'Calendar and event management',
            'branch' => 'master',
            'package' => 'nextdeveloper/agenda',
            'publishable' => [
                'config' => true,
                'migrations' => true,
            ],
            'provider' => 'NextDeveloper\Agenda\AgendaServiceProvider',
            'dependencies' => ['commons'],
        ],
        'communication' => [
            'name' => 'Communication',
            'description' => 'Communication and messaging services',
            'branch' => 'master',
            'package' => 'nextdeveloper/communication',
            'publishable' => [
                'config' => true,
                'migrations' => true,
            ],
            'provider' => 'NextDeveloper\Communication\CommunicationServiceProvider',
            'dependencies' => ['commons'],
        ],
        'iaas' => [
            'name' => 'IAAS',
            'description' => 'Infrastructure as a Service',
            'branch' => 'master',
            'package' => 'nextdeveloper/iaas',
            'publishable' => [
                'config' => true,
                'migrations' => true,
            ],
            'provider' => 'NextDeveloper\IAAS\IAASServiceProvider',
            'dependencies' => ['commons'],
        ],
        'marketplace' => [
            'name' => 'Marketplace',
            'description' => 'Marketplace integration and features',
            'branch' => 'master',
            'package' => 'nextdeveloper/marketplace',
            'publishable' => [
                'config' => true,
                'migrations' => false,
            ],
            'provider' => 'NextDeveloper\Marketplace\MarketplaceServiceProvider',
            'dependencies' => ['commons'],
        ],
    ],
    
    'requirements' => [
        'php' => '8.1.0',
        'composer' => true,
    ],
]; 