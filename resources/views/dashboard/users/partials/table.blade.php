<table class="w-full text-start border-collapse">
    <thead>
        <tr class="bg-muted/40 border-b border-border text-muted-foreground font-extrabold uppercase text-[10px] tracking-wider text-start">
            <th class="p-4 text-start font-extrabold">{{ __('messages.full_name') }}</th>
            <th class="p-4 text-start font-extrabold">{{ __('messages.email') }}</th>
            <th class="p-4 text-start font-extrabold">{{ __('messages.role') }}</th>
            <th class="p-4 text-start font-extrabold">{{ __('messages.last_active') }}</th>
            <th class="p-4 text-end font-extrabold no-print">{{ __('messages.actions') }}</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-border text-xs font-semibold text-foreground">
        @forelse($filteredUsers as $user)
            <tr class="hover:bg-muted/30 transition-colors">
                <td class="p-4 font-bold text-foreground">
                    {{ app()->getLocale() == 'ar' ? $user->name_ar : $user->name_en }}
                </td>
                <td class="p-4 text-muted-foreground font-medium">{{ $user->email }}</td>
                <td class="p-4">
                    <!-- Role Badge -->
                    @if($user->role == 'super_admin')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/30">
                            {{ __('messages.role_super_admin') }}
                        </span>
                    @elseif($user->role == 'projects_officer')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900/30">
                            {{ __('messages.role_projects_officer') }}
                        </span>
                    @elseif($user->role == 'alumni_officer')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 dark:bg-sky-950/20 text-sky-700 dark:text-sky-400 border border-sky-100 dark:border-sky-900/30">
                            {{ __('messages.role_alumni_officer') }}
                        </span>
                    @elseif($user->role == 'admin')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 dark:bg-purple-950/20 text-purple-700 dark:text-purple-400 border border-purple-100 dark:border-purple-900/30">
                            {{ __('messages.role_admin') }}
                        </span>
                    @elseif($user->role == 'supervisor')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30">
                            {{ __('messages.role_supervisor') }}
                        </span>
                    @elseif($user->role == 'student')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-50 dark:bg-emerald-950/20 text-green-700 dark:text-green-400 border border-green-100 dark:border-emerald-900/30">
                            {{ __('messages.role_student') }}
                        </span>
                    @elseif($user->role == 'alumnus')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-400 border border-amber-100 dark:border-amber-900/30">
                            {{ __('messages.role_alumni') }}
                        </span>
                    @endif
                </td>
                <td class="p-4 text-muted-foreground/80 font-medium">
                    {{ $user->last_active_at ? $user->last_active_at->diffForHumans() : __('messages.last_active') }}
                </td>
                
                <!-- Actions -->
                <td class="p-4 text-end no-print">
                    <div class="inline-flex items-center gap-2 justify-end">
                        <!-- Role select updater -->
                        <form action="{{ route('users.update', $user->id) }}" method="POST" class="inline">
                            @csrf
                            <select name="role" onchange="if(confirm('هل أنت متأكد من تغيير دور هذا المستخدم؟')) { this.form.submit(); } else { this.value='{{ $user->role }}'; }" class="text-[10px] font-bold px-2 py-1.5 bg-card text-foreground border border-border rounded-lg outline-none">
                                <option value="student" {{ $user->role == 'student' ? 'selected' : '' }}>{{ __('messages.role_student') }}</option>
                                <option value="supervisor" {{ $user->role == 'supervisor' ? 'selected' : '' }}>{{ __('messages.role_supervisor') }}</option>
                                <option value="alumnus" {{ $user->role == 'alumnus' ? 'selected' : '' }}>{{ __('messages.role_alumni') }}</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>{{ __('messages.role_admin') }}</option>
                                <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>{{ __('messages.role_super_admin') }}</option>
                                <option value="projects_officer" {{ $user->role == 'projects_officer' ? 'selected' : '' }}>{{ __('messages.role_projects_officer') }}</option>
                                <option value="alumni_officer" {{ $user->role == 'alumni_officer' ? 'selected' : '' }}>{{ __('messages.role_alumni_officer') }}</option>
                            </select>
                        </form>

                        <!-- Edit button -->
                        <button type="button" onclick="openEditUserModal({
                            id: {{ $user->id }},
                            name_ar: '{{ addslashes($user->name_ar) }}',
                            name_en: '{{ addslashes($user->name_en) }}',
                            email: '{{ addslashes($user->email) }}',
                            phone: '{{ addslashes($user->phone) }}',
                            department: '{{ addslashes($user->department) }}',
                            role: '{{ addslashes($user->role) }}'
                        })" class="p-1.5 border border-border hover:border-blue-200 dark:hover:border-blue-900 rounded-lg text-muted-foreground hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-950/25 transition-colors cursor-pointer" title="{{ __('messages.edit') }}">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </button>

                        <!-- Delete button (if not self) -->
                        @if($user->id !== auth()->id())
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                @csrf
                                <button type="submit" class="p-1.5 border border-border hover:border-rose-200 dark:hover:border-rose-900 rounded-lg text-muted-foreground hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/25 transition-colors cursor-pointer" title="{{ __('messages.delete') }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-muted-foreground/60 italic font-bold">
                    لا يوجد مستخدمون في هذه القائمة.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
