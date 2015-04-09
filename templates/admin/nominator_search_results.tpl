<table class="table table-striped">
    <tr>
        <th class="name-sort">Name <br />
            {FIRST_NAME_SORT} {MIDDLE_NAME_SORT} {LAST_NAME_SORT}
        </th>
        <th>Email
        </th>
        <th>{ADDED_ON_SORT}
        </th>
        <th>Nominated
        </th>
    </tr>
    <!-- BEGIN listrows -->
    <tr>
        <td>{LINK}
        </td>
        <td>{EMAIL}
        </td>
        <td>{ADDED_ON}
        </td>
        <td>{NOMINEE_LINK}
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
