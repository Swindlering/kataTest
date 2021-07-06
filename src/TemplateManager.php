<?php

namespace App;

use App\Entity\Lesson;
use App\Entity\Learner;
use App\Entity\Template;
use App\Repository\LessonRepository;
use App\Repository\MeetingPointRepository;
use App\Repository\InstructorRepository;
use App\Context\ApplicationContext;

class TemplateManager
{
    private $lessonRepository;
    private $meetingPointRepository;
    private $instructorRepository;
    private $applicationContext;

    public function __construct()
    {
        $this->applicationContext = ApplicationContext::getInstance();
        $this->lessonRepository = LessonRepository::getInstance();
        $this->meetingPointRepository  = MeetingPointRepository::getInstance();
        $this->instructorRepository = InstructorRepository::getInstance();
    }
    
    public function getTemplateComputed(Template $tpl, array $data)
    {
        $replaced = clone($tpl);
        try {
            $replaced->subject = $this->computeText($replaced->subject, $data);
            $replaced->content = $this->computeText($replaced->content, $data);
        } catch (\Exception $e) {
            throw new \Exception( $e->getMessage());
        }

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $lesson = (isset($data['lesson']) and $data['lesson'] instanceof Lesson) ? $data['lesson'] : null;
        if (is_null($lesson)) {
            throw new \Exception( 'No lesson Found');
        }
        $oLesson = $this->lessonRepository->getById($lesson->getId());

        // manage summary_html
        $this->replaceIntoText('[lesson:summary_html]', Lesson::renderHtml($oLesson), $text);
        // manage summary
        $this->replaceIntoText('[lesson:summary]', Lesson::renderHtml($oLesson), $text);
        (strpos($text, '[lesson:instructor_name]')) and $text = str_replace('[lesson:instructor_name]', $this->instructorRepository->getFirstname(), $text);

        if ($lesson->getMeetingPointId()) {
            // manage meeting point
            $this->replaceIntoText('[lesson:meeting_point]', $this->meetingPointRepository->getName(), $text);
        }

        // manage date and time
        $this->replaceIntoText('[lesson:start_date]', $lesson->getStartTime()->format('d/m/Y'), $text);
        $this->replaceIntoText('[lesson:start_time]', $lesson->getStartTime()->format('H:i'), $text);
        $this->replaceIntoText('[lesson:end_time]', $lesson->getStartTime()->format('H:i'), $text);
        
        $link =  (strpos($text, '[lesson:instructor_link]'))
            ?$this->meetingPointRepository->getUrl() . '/' . $this->instructorRepository->getId() . '/lesson/' . $oLesson->getId()
            : '';
        $this->replaceIntoText('[lesson:instructor_link]', $link, $text);

        /*
         * USER
         * [user:*]
         */
        $_user  = (isset($data['user'])  and ($data['user']  instanceof Learner))  ? $data['user']  : $this->applicationContext->getCurrentUser();
        if ($_user) {
            (strpos($text, '[user:first_name]')) and $text = str_replace('[user:first_name]', ucfirst(mb_strtolower($_user->firstname)), $text);
        }

        return $text;
    }
    /**
     *
     */
    public function replaceIntoText($search, $replace, &$text)
    {
        if (strpos($text, $search)) {
            $text = str_replace(
                $search,
                $replace,
                $text
            );
        }
    }
}
