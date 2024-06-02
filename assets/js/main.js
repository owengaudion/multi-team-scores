function renameTeam(teamKey, teamName) {
    var form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';

    var teamKeyInput = document.createElement('input');
    teamKeyInput.name = 'team_key';
    teamKeyInput.value = teamKey;
    form.appendChild(teamKeyInput);

    var teamNameInput = document.createElement('input');
    teamNameInput.name = 'team_name';
    teamNameInput.value = teamName;
    form.appendChild(teamNameInput);

    var renameTeamInput = document.createElement('input');
    renameTeamInput.name = 'rename_team';
    renameTeamInput.value = '1';
    form.appendChild(renameTeamInput);

    document.body.appendChild(form);
    form.submit();
}

function removeTeam(teamKey) {
    if (confirm('Are you sure you want to remove this team?')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';

        var teamKeyInput = document.createElement('input');
        teamKeyInput.name = 'team_key';
        teamKeyInput.value = teamKey;
        form.appendChild(teamKeyInput);

        var removeTeamInput = document.createElement('input');
        removeTeamInput.name = 'remove_team';
        removeTeamInput.value = '1';
        form.appendChild(removeTeamInput);

        document.body.appendChild(form);
        form.submit();
    }
}