<?php

namespace HeVinci\CompetencyBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use JMS\SecurityExtraBundle\Annotation as SEC;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;

/**
 * @DI\Tag("security.secure_service")
 * @SEC\PreAuthorize("canOpenAdminTool('learning-objectives')")
 */
class LearningObjectiveController
{
    /**
     * Displays the index of the learning objectives tool, i.e.
     * the list of learning objectives.
     *
     * @EXT\Route("/objectives", name="hevinci_objectives_index")
     * @EXT\Template
     *
     * @return array
     */
    public function objectivesAction()
    {
        return [];
    }
}
