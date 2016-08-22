<?php

namespace smtech\CanvasHack;

class Toolbox extends \smtech\StMarksReflexiveCanvasLTI\Toolbox
{
    /**
     * Configure account navigation placement
     *
     * @return Generator
     */
    public function getGenerator()
    {
        parent::getGenerator();

        $this->generator->setOptionProperty(
            Option::COURSE_NAVIGATION(),
            'visibility',
            'admins'
        );

        return $this->generator;
    }

    public static loadConfi
}
