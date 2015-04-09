{START_FORM}
<h2>Admin Settings</h2>
<table class="table table-striped">
    <tr>
        <th>{FILE_DIR_LABEL}
        </th>
        <td>{FILE_DIR}
        </td>
    </tr>

    <tr>
        <th>{AWARD_TITLE_LABEL}
        </th>
        <td>{AWARD_TITLE}
        </td>
    </tr>

    <tr>
        <th>Allowed File Types:
        </th>
        <td>
            <!-- BEGIN allowed_file_types_repeat -->
            {ALLOWED_FILE_TYPES}{ALLOWED_FILE_TYPES_LABEL}<br />
            <!-- END allowed_file_types_repeat -->
        </td>
    </tr>

    <tr>
        <th>{EMAIL_FROM_ADDRESS_LABEL}</th>
        <td>{EMAIL_FROM_ADDRESS}</td>
    </tr>
</table>
{SUBMIT}
{END_FORM}
