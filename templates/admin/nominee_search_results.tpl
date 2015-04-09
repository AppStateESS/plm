<table class="table striped">
    <tr>
        <th class="name-sort">Name <br />
            {FIRST_NAME_SORT} / {LAST_NAME_SORT}
        </th>
        <th>Email
        </th>
        <th>Major
        </th>
        <th>Years
        </th>
    </tr>
    <!-- BEGIN listrows -->
    <tr>
        <td>{LINK}</td>
        <td> {EMAIL}
        </td>
        <td>{MAJOR}
        </td>
        <td>{YEARS}
        </td>
    </tr>
    <!-- END listrows -->
    <!-- BEGIN EMPTY_MESSAGE -->
    <tr>
        <td colspan="6"><i>{EMPTY_MESSAGE}</i></td>
    </tr>
    <!-- END EMPTY_MESSAGE -->
</table>

<div align="center">
    <b>{PAGE_LABEL}</b><br />
    {PAGES}<br />
    {LIMITS}
</div>
