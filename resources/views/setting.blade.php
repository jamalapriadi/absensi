@extends('layouts.coreui')

@section('content')
	<div class="row">
    <div class="card">
      <div class="card-header">
        Card title
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" data-toggle="tab" href="#home" role="tab" aria-controls="home">Profile</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#pangkat" role="tab" aria-controls="profile">Pangkat</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#golongan" role="tab" aria-controls="messages">Golongan</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-toggle="tab" href="#status" role="tab" aria-controls="messages">Status</a>
            </li>
        </ul>

        <div class="tab-content">
          <div class="tab-pane active" id="home" role="tabpanel">
            1. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure
            dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
          </div>
          <div class="tab-pane" id="pangkat" role="tabpanel">
            2. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure
            dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
          </div>
          <div class="tab-pane" id="golongan" role="tabpanel">
            3. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure
            dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
          </div>
          <div class="tab-pane" id="status" role="tabpanel">
          
          </div>
        </div>
      </div>
    </div>
	</div>
@stop