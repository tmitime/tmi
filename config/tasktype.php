<?php

return [

    'default' => 'tmi:Task',

    'types' => [

        /* Define the type of Tasks and their hierarchical relation */

        // Types are like concepts in an ontology, in fact
        // they can be sub-class of or equal to another concept

        // format => prefix : name

        'schema:Event' => null,

        'tmi:Task' => null,
        'tmi:Meeting' => ['isa' => 'schema:Event', 'subclassof' => 'tmi:Task'],
        'tmi:Review' => ['subclassof' => 'tmi:Task', 'isa' => 'schema:ReviewAction'],
        // 'tmi:RefinementAction' => ['subclassof' => 'schema:Action'],
        
        'scrum:DailyScrum' => ['subclassof' => 'tmi:Meeting'],
        'scrum:Planning' => ['subclassof' => 'tmi:Meeting'],
        'scrum:Review' => ['subclassof' => 'tmi:Meeting'],
        'scrum:Retrospective' => ['subclassof' => 'tmi:Meeting'],
        'scrum:ProductBacklogRefinement' => ['subclassof' => 'tmi:Meeting'],
        'scrum:Standup' => ['isa' => 'scrum:DailyScrum'],
        'scrum:Daily' => ['isa' => 'scrum:DailyScrum'],
        'scrum:BacklogRefinement' => ['isa' => 'scrum:ProductBacklogRefinement'],
        'scrum:Grooming' => ['isa' => 'scrum:ProductBacklogRefinement'],

    ],

];
