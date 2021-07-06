<?php
namespace App\Tests;

use PHPUnit\Framework\TestCase;
use \Faker\Factory;
use App\TemplateManager;
use App\Entity\Lesson;
use App\Entity\Template;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\Repository\InstructorRepository;
use App\Context\ApplicationContext;

class TemplateManagerTest extends TestCase
{
    /**
     * Init the mocks
     */
    public function setUp()
    {
        $this->faker = Factory::create();

        $this->instructorRepository = InstructorRepository::getInstance();
        $this->meetingPointRepository = MeetingPointRepository::getInstance();
        $this->applicationContext = ApplicationContext::getInstance();
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @replaceIntoText
     */
    public function testReplaceIntoTextOK()
    {
        $templateManager = new TemplateManager();
        $search =  '[search:search]';
        $replace =  '29,99';
        $text = 'Ornikar, Auto-École en Ligne - Obtenez votre Code pour [search:search]€';
        $templateManager->replaceIntoText($search, $replace, $text);
        $this->assertEquals('Ornikar, Auto-École en Ligne - Obtenez votre Code pour 29,99€', $text);
    }

    /**
     * @replaceIntoText
     */
    public function testReplaceIntoTextNothingMatched()
    {
        $templateManager = new TemplateManager();
        $search =  '[searching:searching]';
        $replace =  '29,99';
        $text = 'Ornikar, Auto-École en Ligne - Obtenez votre Code pour [search:search]€';
        $templateManager->replaceIntoText($search, $replace, $text);
        $this->assertEquals('Ornikar, Auto-École en Ligne - Obtenez votre Code pour [search:search]€', $text);
    }
    /**
     * @expectedException Exception
     */
    public function testGetTemplateComputedKo()
    {
        $template = $this->getTemplate();
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => null
            ]
        );
    }
    /**
     * @getTemplateComputed
     */
    public function testGetTemplateComputed()
    {
        $expectedInstructor =  $this->instructorRepository->getById($this->faker->randomNumber());
        $expectedMeetingPoint = $this->meetingPointRepository->getById($this->faker->randomNumber());
        $expectedUser =  $this->applicationContext->getCurrentUser();
        
        $start_at = $this->faker->dateTimeBetween("-1 month");
        $end_at = $start_at->add(new \DateInterval('PT1H'));

        $lesson = new Lesson($this->faker->randomNumber(), $this->faker->randomNumber(), $this->faker->randomNumber(), $start_at, $end_at);

        $template = $this->getTemplate();
        $templateManager = new TemplateManager();

        $message = $templateManager->getTemplateComputed(
            $template,
            [
                'lesson' => $lesson
            ]
        );

        $this->assertEquals('Votre leçon de conduite avec ' . $expectedInstructor->firstname, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

La reservation du " . $start_at->format('d/m/Y') . " de " . $start_at->format('H:i') . " à " . $end_at->format('H:i') . " avec " . $expectedInstructor->firstname . " a bien été prise en compte!
Voici votre point de rendez-vous: " . $expectedMeetingPoint->name . ".

Bien cordialement,

L'équipe Ornikar
", $message->content);
    }

    private function getTemplate(){
        return new Template(
            1,
            'Votre leçon de conduite avec [lesson:instructor_name]',
            "
Bonjour [user:first_name],

La reservation du [lesson:start_date] de [lesson:start_time] à [lesson:end_time] avec [lesson:instructor_name] a bien été prise en compte!
Voici votre point de rendez-vous: [lesson:meeting_point].

Bien cordialement,

L'équipe Ornikar
");
    }
}
