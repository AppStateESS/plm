<h2>{NAME}</h2>
<h3>{EMAIL}</h3>
<span id="winner-tag">{WINNER}</span>

<div><b>Major:</b> {MAJOR}</div>
<div><b>Years:</b> {YEARS}</div>
<div>
    <!-- BEGIN nominations -->
    <div class="nom-toggle-header" >
        <span class="nom-toggle" id="nom-toggle-{NUM}"><img src="{DOWN_PNG_HACK}" /> Nomination {NUM}</span>
        <span class="nom-manager nom-{NUM}">
            <img id="icon" src="{ICON}"/>
            <img id="award-icon" src="{AWARD_ICON}"/>
            <span id="help-text"></span>
        </span>
    </div>
    <div id="{NUM}">{CONTENT}
    </div>
    <!-- END nominations -->
</div>
