<?php

require_once '../vendor/autoload.php';

use Microsoft\Graph\Graph;
use Microsoft\Graph\Generated\Models\Event;
use Microsoft\Graph\Generated\Models\ItemBody;
use Microsoft\Graph\Generated\Models\BodyType;
use Microsoft\Graph\Generated\Models\DateTimeTimeZone;
use Microsoft\Graph\Generated\Models\Location;
use Microsoft\Graph\Generated\Models\OnlineMeetingProviderType;
use Microsoft\Graph\Generated\Models\Attendee;
use Microsoft\Graph\Generated\Models\EmailAddress;

class TeamsMeetingProvider {
    private $graphClient;

    public function __construct($accessToken) {
        // Initialize Graph client
        $this->graphClient = new Graph();
        $this->graphClient->setAccessToken($accessToken);
    }

    public function createMeeting($subject, $startDateTime, $endDateTime, $attendees) {
        try {
            $event = new Event();
            $event->setSubject($subject);
            
            // Set meeting body
            $body = new ItemBody();
            $body->setContentType(BodyType::HTML);
            $body->setContent('Please join the meeting.');
            $event->setBody($body);
            
            // Set start and end times
            $start = new DateTimeTimeZone();
            $start->setDateTime($startDateTime);
            $start->setTimeZone('Pacific Standard Time');
            $event->setStart($start);
            
            $end = new DateTimeTimeZone();
            $end->setDateTime($endDateTime);
            $end->setTimeZone('Pacific Standard Time');
            $event->setEnd($end);

            // Set location (optional)
            $location = new Location();
            $location->setDisplayName('Online Meeting');
            $event->setLocation($location);
            
            // Set attendees
            $attendeesArray = [];
            foreach ($attendees as $email) {
                $attendee = new Attendee();
                $emailAddress = new EmailAddress();
                $emailAddress->setAddress($email);
                $attendee->setEmailAddress($emailAddress);
                $attendee->setType("required"); // Can be 'required' or 'optional'
                $attendeesArray[] = $attendee;
            }
            $event->setAttendees($attendeesArray);

            // Set online meeting properties
            $event->setIsOnlineMeeting(true);
            $event->setOnlineMeetingProvider(OnlineMeetingProviderType::TEAMS_FOR_BUSINESS);

            // Send the request to create the event
            $createdEvent = $this->graphClient->me()->events()->create($event)->wait();
            return $createdEvent;

        } catch (Exception $e) {
            // Handle exceptions
            echo 'Error creating meeting: ' . $e->getMessage();
            return null;
        }
    }
}
