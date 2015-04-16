<?php

/**
 * Tells you whether or not a field is visible.
 * @author Jeff Tickle <jtickle at tux dot appstate dot edu>
 */

class NominationFieldVisibility
{
    protected $visibility;
    protected $fields;

    public function __construct()
    {
        $this->fields = $this->createFields();
        $this->visibility = $this->loadVisibility();
    }

    public function isVisible($field)
    {
        return in_array($field, $this->visibility);
    }

    protected function loadVisibility()
    {
        $vis = array();
        foreach($this->fields as $field) {
            // If it's not set, show it.
            if(!PHPWS_Settings::is_set('nomination', 'field_' . $field)) {
                $vis[] = $field;
                continue;
            }
            // Otherwise, go to settings.
            if(PHPWS_Settings::get('nomination', 'field_' . $field) == 1) {
                $vis[] = $field;
            }
        }
        return $vis;
    }

    protected function createFields()
    {
        return array('nominee_asubox',
                     'nominee_position',
                     'nominee_department_major',
                     'nominee_years',
                     'nominee_responsibility',
                     'nominee_banner_id',
                     'nominee_phone',
                     'nominee_gpa',
                     'nominee_class',
                     'category',
                     'reference_first_name',
                     'reference_last_name',
                     'reference_department',
                     'reference_email',
                     'reference_phone',
                     'reference_relationship',
                     'statement',
                     'nominator_first_name',
                     'nominator_middle_name',
                     'nominator_last_name',
                     'nominator_address',
                     'nominator_phone',
                     'nominator_email',
                     'nominator_relationship');
    }

    public function prepareSettingsForm(PHPWS_Form $form, $name)
    {
        $fieldMatch = array();
        foreach($this->fields as $field) {
            if($this->isVisible($field)) {
                $fieldMatch[$field] = $field;
            }
        }

        $form->addCheckAssoc($name, array_combine($this->fields, $this->fields));
        $form->setMatch('show_fields', $fieldMatch);
    }

    public function saveFromContext(Context $context, $name)
    {
        // Mark all fields hidden first
        foreach($this->fields as $field) {
            PHPWS_Settings::set('nomination', 'field_' . $field, 0);
        }

        // Now mark them shown if they show up on the request
        foreach($context[$name] as $value) {
            PHPWS_Settings::set('nomination', 'field_' . $value, 1);
        }

        PHPWS_Settings::save('nomination');
        $this->visibility = $this->loadVisibility();
    }
}

?>
