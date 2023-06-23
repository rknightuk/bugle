<div class="admin_profile_header">
    <div>
        <img src="{{ $profile->getAvatarPath() }}" width="100">
    </div>
    <div>
        <p>
            <strong>{{ $profile->name }}</strong>
            <br>
            {{"@"}}{{ $profile->username }}
            <br>
            <a href="/{{"@"}}{{ $profile->username }}" target="_blank">View Profile</a>
        </p>
    </div>
</div>
