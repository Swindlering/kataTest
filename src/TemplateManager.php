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
    }
    
    public function getTemplateComputed(Template $tpl, array $data)
    {
        if (!$tpl) {
            throw new \RuntimeException('no tpl given');
        }

        $replaced = clone($tpl);
        $replaced->subject = $this->computeText($replaced->subject, $data);
        $replaced->content = $this->computeText($replaced->content, $data);

        return $replaced;
    }

    private function computeText($text, array $data)
    {
        $lesson = (isset($data['lesson']) and $data['lesson'] instanceof Lesson) ? $data['lesson'] : null;

        $this->lessonRepository = LessonRepository::getInstance()->getById($lesson->id);
        $this->meetingPointRepository  = MeetingPointRepository::getInstance()->getById($lesson->meetingPointId);
        $this->instructorRepository = InstructorRepository::getInstance()->getById($lesson->instructorId);

        if ($lesson) {
            $containsSummaryHtml = strpos($text, '[lesson:summary_html]');
            $containsSummary     = strpos($text, '[lesson:summary]');

            if ($containsSummaryHtml) {
                $text = str_replace(
                    '[lesson:summary_html]',
                    Lesson::renderHtml($this->lessonRepository),
                    $text
                );
            }
            if ($containsSummary) {
                $text = str_replace(
                    '[lesson:summary]',
                    Lesson::renderText($this->lessonRepository),
                    $text
                );
            }

            (strpos($text, '[lesson:instructor_name]')) and $text = str_replace('[lesson:instructor_name]', $this->instructorRepository->firstname, $text);
        }

        if ($lesson->meetingPointId) {
            if (strpos($text, '[lesson:meeting_point]')) {
                $text = str_replace('[lesson:meeting_point]', $this->meetingPointRepository->name, $text);
            }
        }

        if (strpos($text, '[lesson:start_date]')) {
            $text = str_replace('[lesson:start_date]', $lesson->start_time->format('d/m/Y'), $text);
        }

        if (strpos($text, '[lesson:start_time]')) {
            $text = str_replace('[lesson:start_time]', $lesson->start_time->format('H:i'), $text);
        }

        if (strpos($text, '[lesson:end_time]')) {
            $text = str_replace('[lesson:end_time]', $lesson->start_time->format('H:i'), $text);
        }

        if (strpos($text, '[lesson:instructor_link]')) {
            $text = str_replace('[lesson:link]', $this->meetingPointRepository->url . '/' . $this->instructorRepository->id . '/lesson/' . $this->lessonRepository->id, $text);
        } else {
            $text = str_replace('[lesson:link]', '', $text);
        }

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
}
