{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<!-- <li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li> -->


<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i>Пользователи</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('section') }}"><i class="nav-icon la la-sitemap"></i> Разделы</a></li>
<!-- la la-male это pupiles -->

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('theme') }}"><i class="nav-icon la la-comment"></i> Темы</a></li>
<!-- <li class="nav-item"><a class="nav-link" href="{{ backpack_url('paragraph') }}"><i class="nav-icon la la-reorder"></i> Параграфы</a></li> -->

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user_extended') }}"><i class="nav-icon la la-user-plus"></i> Расширенная панель управления пользователями</a></li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('paragraphs_edit') }}"><i class="nav-icon la la-dedent"></i> Абзацы</a></li>


<li class="nav-item"><a class="nav-link" href="{{ backpack_url('elfinder') }}"><i class="nav-icon la la-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li>