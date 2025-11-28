<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verdant Glass - Teacher Portal</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* --- Design System: Verdant Glass --- */
        :root {
            --forest: #0D3B2E;
            --sage: #E8F5E9;
            --yellow: #F4D35E;
            --coral: #EF5350;
            --white: #FFFFFF;
            --glass-bg: rgba(255, 255, 255, 0.65);
            --glass-border: rgba(255, 255, 255, 0.4);
            --glass-shadow: 0 8px 32px 0 rgba(13, 59, 46, 0.05);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F3F4F6;
            color: var(--forest);
            overflow-x: hidden;
        }

        /* Animated Background Blobs */
        .blob-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            overflow: hidden;
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 10s infinite alternate ease-in-out;
        }
        .blob-1 { top: -10%; left: -10%; width: 500px; height: 500px; background: #D1FAE5; } /* Light Green */
        .blob-2 { bottom: -10%; right: -10%; width: 600px; height: 600px; background: #FEF3C7; animation-delay: 2s; } /* Light Yellow */

        @keyframes float {
            0% { transform: translate(0, 0); }
            100% { transform: translate(30px, 50px); }
        }

        /* Glass Component Utility */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
        }

        /* Custom Scrollbar for Tables */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: rgba(13, 59, 46, 0.2); border-radius: 10px; }

        /* Input Styles */
        .grade-input {
            background: transparent;
            border-bottom: 2px solid rgba(13, 59, 46, 0.1);
            text-align: center;
            transition: all 0.2s;
        }
        .grade-input:focus {
            outline: none;
            border-bottom-color: var(--forest);
            background: rgba(255,255,255,0.5);
        }

        /* Smooth View Transition */
        .view-section {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .hidden-view {
            display: none;
            opacity: 0;
            transform: translateY(10px);
        }
        .active-view {
            display: grid; /* or block depending on view */
            opacity: 1;
            transform: translateY(0);
        }

        /* Navigation Active State */
        .nav-item.active {
            background: rgba(13, 59, 46, 0.08);
            border-right: 3px solid var(--forest);
        }
    </style>
</head>
<body class="flex h-screen w-screen">

    <!-- Background Art -->
    <div class="blob-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <!-- Sidebar Navigation -->
    <aside class="w-20 lg:w-64 glass h-full flex flex-col justify-between z-20 transition-all duration-300">
        <div>
            <!-- Logo Area -->
            <div class="h-24 flex items-center justify-center lg:justify-start lg:px-8 border-b border-white/40">
                <div class="w-10 h-10 bg-[#0D3B2E] rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                    G
                </div>
                <span class="ml-3 font-bold text-xl hidden lg:block text-[#0D3B2E]">GradePortal</span>
            </div>

            <!-- Nav Links -->
            <nav class="mt-8 flex flex-col gap-2">
                <a href="#" onclick="switchView('dashboard')" id="nav-dash" class="nav-item active h-12 flex items-center px-4 lg:px-8 text-[#0D3B2E] hover:bg-white/40 transition-colors">
                    <i class="ph ph-squares-four text-2xl"></i>
                    <span class="ml-4 font-medium hidden lg:block">Overview</span>
                </a>
                <a href="#" class="nav-item h-12 flex items-center px-4 lg:px-8 text-[#0D3B2E]/70 hover:bg-white/40 transition-colors">
                    <i class="ph ph-chalkboard-teacher text-2xl"></i>
                    <span class="ml-4 font-medium hidden lg:block">Classes</span>
                </a>
                <a href="#" class="nav-item h-12 flex items-center px-4 lg:px-8 text-[#0D3B2E]/70 hover:bg-white/40 transition-colors">
                    <i class="ph ph-users text-2xl"></i>
                    <span class="ml-4 font-medium hidden lg:block">Students</span>
                </a>
                <a href="#" class="nav-item h-12 flex items-center px-4 lg:px-8 text-[#0D3B2E]/70 hover:bg-white/40 transition-colors">
                    <i class="ph ph-chat-circle-text text-2xl"></i>
                    <span class="ml-4 font-medium hidden lg:block">Messages</span>
                    <span class="ml-auto w-2 h-2 bg-[#F4D35E] rounded-full hidden lg:block"></span>
                </a>
            </nav>
        </div>

        <!-- Bottom Profile -->
        <div class="p-4 lg:p-6 border-t border-white/40">
            <div class="flex items-center gap-3">
                <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=Felix" alt="Profile" class="w-10 h-10 rounded-full border-2 border-white shadow-sm bg-white">
                <div class="hidden lg:block">
                    <p class="text-sm font-bold text-[#0D3B2E]">Mr. Reyes</p>
                    <p class="text-xs opacity-60">Senior Teacher</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 h-full overflow-y-auto p-4 lg:p-8 custom-scroll relative">
        
        <!-- HEADER -->
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-[#0D3B2E]">Dashboard</h1>
                <p class="text-sm opacity-60">Welcome back to the Spring Semester.</p>
            </div>
            <div class="flex gap-4">
                <button class="w-10 h-10 rounded-full glass flex items-center justify-center text-[#0D3B2E] hover:bg-white transition">
                    <i class="ph ph-bell text-xl"></i>
                </button>
                <button class="w-10 h-10 rounded-full glass flex items-center justify-center text-[#0D3B2E] hover:bg-white transition">
                    <i class="ph ph-magnifying-glass text-xl"></i>
                </button>
            </div>
        </header>


        <!-- VIEW 1: BENTO DASHBOARD -->
        <div id="view-dashboard" class="view-section active-view grid grid-cols-1 md:grid-cols-3 md:grid-rows-3 gap-6 max-w-7xl mx-auto h-auto md:h-[calc(100vh-140px)]">

            <!-- 1. Class Overview (Top Left) -->
            <div class="glass rounded-3xl p-6 md:col-span-2 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#E8F5E9] rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>
                
                <div class="flex justify-between items-start z-10">
                    <div>
                        <h2 class="text-lg font-bold text-[#0D3B2E]/90">Class Progress</h2>
                        <p class="text-xs uppercase tracking-wider opacity-60 mt-1">Active Sections</p>
                    </div>
                    <button class="text-sm underline opacity-70 hover:opacity-100">View All</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 z-10">
                    <!-- Mini Card 1 -->
                    <div class="bg-white/40 p-4 rounded-2xl border border-white/30 hover:bg-white/60 transition cursor-pointer">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold text-sm">Science 10-A</span>
                            <i class="ph ph-flask text-[#0D3B2E]"></i>
                        </div>
                        <div class="w-full bg-white/50 h-2 rounded-full mt-2">
                            <div class="bg-[#0D3B2E] h-2 rounded-full" style="width: 75%"></div>
                        </div>
                        <p class="text-[10px] mt-2 opacity-70">Unit 4: Thermodynamics</p>
                    </div>
                    <!-- Mini Card 2 -->
                    <div class="bg-white/40 p-4 rounded-2xl border border-white/30 hover:bg-white/60 transition cursor-pointer">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold text-sm">Physics 101</span>
                            <i class="ph ph-atom text-[#0D3B2E]"></i>
                        </div>
                        <div class="w-full bg-white/50 h-2 rounded-full mt-2">
                            <div class="bg-[#F4D35E] h-2 rounded-full" style="width: 45%"></div>
                        </div>
                        <p class="text-[10px] mt-2 opacity-70">Unit 2: Velocity</p>
                    </div>
                    <!-- Mini Card 3 -->
                    <div class="bg-white/40 p-4 rounded-2xl border border-white/30 hover:bg-white/60 transition cursor-pointer">
                        <div class="flex justify-between mb-2">
                            <span class="font-bold text-sm">Advisory</span>
                            <i class="ph ph-users-three text-[#0D3B2E]"></i>
                        </div>
                        <div class="w-full bg-white/50 h-2 rounded-full mt-2">
                            <div class="bg-[#0D3B2E] h-2 rounded-full" style="width: 100%"></div>
                        </div>
                        <p class="text-[10px] mt-2 opacity-70">Attendance Complete</p>
                    </div>
                </div>
            </div>

            <!-- 2. Action Center (Top Right) -->
            <div class="glass rounded-3xl p-6 flex flex-col justify-center gap-3">
                <h3 class="text-sm font-bold uppercase tracking-wide opacity-60 mb-1">Quick Actions</h3>
                
                <button onclick="switchView('grading')" class="w-full bg-[#0D3B2E] text-white py-3 px-4 rounded-xl flex items-center justify-between hover:bg-[#0D3B2E]/90 transition shadow-lg group">
                    <span class="font-medium text-sm">Input Grades</span>
                    <i class="ph ph-pencil-simple group-hover:translate-x-1 transition"></i>
                </button>
                
                <button class="w-full bg-transparent border border-[#0D3B2E]/20 text-[#0D3B2E] py-3 px-4 rounded-xl flex items-center justify-between hover:bg-[#0D3B2E]/5 transition">
                    <span class="font-medium text-sm">Create Assignment</span>
                    <i class="ph ph-plus"></i>
                </button>
            </div>

            <!-- 3. Alert Center (Mid Left - Tall) -->
            <div class="glass rounded-3xl p-6 md:row-span-2 overflow-y-auto custom-scroll">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-2 h-2 rounded-full bg-[#F4D35E] animate-pulse"></div>
                    <h2 class="text-lg font-bold">Pending</h2>
                </div>

                <div class="space-y-4">
                    <!-- Task Item -->
                    <div class="p-4 rounded-2xl bg-white/30 border border-white/20 hover:bg-white/50 transition group cursor-pointer">
                        <div class="flex justify-between items-start">
                            <span class="bg-[#F4D35E] text-[#0D3B2E] text-[10px] font-bold px-2 py-1 rounded-full">DUE TODAY</span>
                            <i class="ph ph-caret-right opacity-0 group-hover:opacity-100 transition"></i>
                        </div>
                        <h4 class="font-bold mt-2">Quiz #4: Motion</h4>
                        <p class="text-xs opacity-70 mt-1">Science 10-A • 12 Ungraded</p>
                    </div>

                    <!-- Task Item -->
                    <div class="p-4 rounded-2xl bg-white/30 border border-white/20 hover:bg-white/50 transition group cursor-pointer">
                        <div class="flex justify-between items-start">
                            <span class="bg-white/60 text-[#0D3B2E] text-[10px] font-bold px-2 py-1 rounded-full">TOMORROW</span>
                        </div>
                        <h4 class="font-bold mt-2">Lab Report: Acids</h4>
                        <p class="text-xs opacity-70 mt-1">Chemistry • 28 Ungraded</p>
                    </div>

                    <!-- Task Item -->
                    <div class="p-4 rounded-2xl bg-white/30 border border-white/20 hover:bg-white/50 transition group cursor-pointer">
                        <div class="flex justify-between items-start">
                            <span class="bg-white/60 text-[#0D3B2E] text-[10px] font-bold px-2 py-1 rounded-full">REVIEW</span>
                        </div>
                        <h4 class="font-bold mt-2">Absence Request</h4>
                        <p class="text-xs opacity-70 mt-1">Maria Santos</p>
                    </div>
                </div>
            </div>

            <!-- 4. Performance (Mid Center - Wide) -->
            <div class="glass rounded-3xl p-6 md:col-span-2 flex flex-col justify-between relative">
                <div>
                    <h2 class="text-lg font-bold">Performance Analytics</h2>
                    <p class="text-xs opacity-60">Weekly Average across all sections</p>
                </div>
                
                <!-- CSS Only Chart -->
                <div class="flex items-end justify-between h-32 mt-4 px-4 pb-2 border-b border-[#0D3B2E]/10 gap-4">
                    <div class="flex flex-col items-center gap-2 group w-full">
                        <div class="w-full bg-[#0D3B2E]/80 rounded-t-lg h-[40%] group-hover:bg-[#0D3B2E] transition-all relative">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold opacity-0 group-hover:opacity-100 transition">75%</span>
                        </div>
                        <span class="text-xs font-medium opacity-60">Mon</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group w-full">
                        <div class="w-full bg-[#0D3B2E]/80 rounded-t-lg h-[60%] group-hover:bg-[#0D3B2E] transition-all relative">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold opacity-0 group-hover:opacity-100 transition">82%</span>
                        </div>
                        <span class="text-xs font-medium opacity-60">Tue</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group w-full">
                        <div class="w-full bg-[#0D3B2E]/80 rounded-t-lg h-[55%] group-hover:bg-[#0D3B2E] transition-all relative">
                            <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold opacity-0 group-hover:opacity-100 transition">78%</span>
                        </div>
                        <span class="text-xs font-medium opacity-60">Wed</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group w-full">
                        <div class="w-full bg-[#F4D35E] rounded-t-lg h-[85%] shadow-[0_0_15px_rgba(244,211,94,0.4)] relative">
                             <span class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-bold text-[#0D3B2E]">92%</span>
                        </div>
                        <span class="text-xs font-bold text-[#0D3B2E]">Thu</span>
                    </div>
                    <div class="flex flex-col items-center gap-2 group w-full">
                        <div class="w-full bg-[#0D3B2E]/20 rounded-t-lg h-[5%]"></div>
                        <span class="text-xs font-medium opacity-60">Fri</span>
                    </div>
                </div>
            </div>

            <!-- 5. Up Next (Bottom Center - Solid) -->
            <div class="glass rounded-3xl p-6 bg-[#0D3B2E] text-white border-none shadow-xl flex flex-col justify-center relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 text-white/5">
                    <i class="ph ph-clock text-[120px]"></i>
                </div>
                <h3 class="text-xs uppercase tracking-widest text-white/60 mb-2">Up Next • 10:00 AM</h3>
                <div class="text-3xl font-bold mb-1">Science 10-A</div>
                <p class="text-white/80 text-sm flex items-center gap-2">
                    <i class="ph ph-map-pin"></i> Room 304 (Lab)
                </p>
                <div class="mt-4 flex gap-2">
                    <button class="bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg text-xs backdrop-blur-sm transition">View Roster</button>
                    <button class="bg-[#F4D35E] text-[#0D3B2E] font-bold px-3 py-1.5 rounded-lg text-xs hover:bg-[#F4D35E]/90 transition">Start Class</button>
                </div>
            </div>

            <!-- 6. Timeline (Bottom Right) -->
            <div class="glass rounded-3xl p-6 overflow-y-auto custom-scroll">
                <h3 class="text-sm font-bold uppercase tracking-wide opacity-60 mb-4">Today</h3>
                <div class="border-l-2 border-[#0D3B2E]/10 ml-2 space-y-6">
                    <div class="pl-4 relative">
                        <div class="absolute -left-[5px] top-2 w-2 h-2 rounded-full bg-[#0D3B2E]/40"></div>
                        <p class="text-xs opacity-60">08:00 AM</p>
                        <p class="font-medium text-sm line-through opacity-50">Department Meeting</p>
                    </div>
                    <div class="pl-4 relative">
                        <div class="absolute -left-[5px] top-2 w-2 h-2 rounded-full bg-[#F4D35E] ring-4 ring-[#F4D35E]/20"></div>
                        <p class="text-xs text-[#0D3B2E] font-bold">10:00 AM</p>
                        <p class="font-bold text-sm text-[#0D3B2E]">Science 10-A</p>
                    </div>
                    <div class="pl-4 relative">
                        <div class="absolute -left-[5px] top-2 w-2 h-2 rounded-full bg-[#0D3B2E]/40"></div>
                        <p class="text-xs opacity-60">01:00 PM</p>
                        <p class="font-medium text-sm">Physics 101</p>
                    </div>
                </div>
            </div>

        </div> <!-- End Dashboard View -->


        <!-- VIEW 2: INPUT GRADES (Hidden by Default) -->
        <div id="view-grading" class="view-section hidden-view max-w-5xl mx-auto pb-10">
            
            <!-- Breadcrumbs / Back -->
            <button onclick="switchView('dashboard')" class="flex items-center gap-2 text-sm opacity-60 hover:opacity-100 mb-4 transition">
                <i class="ph ph-arrow-left"></i> Back to Dashboard
            </button>

            <!-- Grading Card -->
            <div class="glass rounded-3xl overflow-hidden min-h-[600px] flex flex-col">
                
                <!-- Toolbar -->
                <div class="p-6 border-b border-white/40 flex flex-col md:flex-row justify-between items-center gap-4 bg-white/20">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#F4D35E] flex items-center justify-center text-[#0D3B2E] text-2xl font-bold shadow-sm">
                            S
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">Science 10-A</h2>
                            <p class="text-sm opacity-70">Quiz #4: Motion & Velocity • Max Score: 100</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button class="px-4 py-2 rounded-xl border border-[#0D3B2E]/20 text-[#0D3B2E] text-sm font-medium hover:bg-[#0D3B2E]/5 transition">Save Draft</button>
                        <button onclick="saveGrades()" class="px-6 py-2 rounded-xl bg-[#0D3B2E] text-white text-sm font-medium shadow-lg hover:bg-[#0D3B2E]/90 transition flex items-center gap-2">
                            <i class="ph ph-check-circle"></i> Publish
                        </button>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="flex-1 overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-xs uppercase tracking-wider text-[#0D3B2E]/60 border-b border-[#0D3B2E]/10">
                                <th class="p-6 font-semibold">Student Name</th>
                                <th class="p-6 font-semibold w-32">Status</th>
                                <th class="p-6 font-semibold w-32 text-center">Score</th>
                                <th class="p-6 font-semibold">Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            
                            <!-- Student Row 1 -->
                            <tr class="hover:bg-white/40 transition group border-b border-white/20">
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=Annie" class="w-8 h-8 rounded-full bg-white shadow-sm">
                                        <div>
                                            <p class="font-bold">Annie Abbot</p>
                                            <p class="text-xs opacity-50">ID: 2024-001</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span id="status-1" class="px-3 py-1 rounded-full bg-[#E8F5E9] text-[#0D3B2E] text-xs font-bold border border-[#0D3B2E]/10">Passing</span>
                                </td>
                                <td class="p-6 text-center">
                                    <input type="number" value="92" min="0" max="100" 
                                        class="grade-input w-16 text-lg font-bold text-[#0D3B2E]"
                                        oninput="updateGradeStatus(this, 'status-1')">
                                </td>
                                <td class="p-6">
                                    <input type="text" placeholder="Add comment..." class="w-full bg-transparent border-none text-sm opacity-60 focus:opacity-100 focus:outline-none placeholder-gray-500">
                                </td>
                            </tr>

                            <!-- Student Row 2 -->
                            <tr class="hover:bg-white/40 transition group border-b border-white/20">
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=Ben" class="w-8 h-8 rounded-full bg-white shadow-sm">
                                        <div>
                                            <p class="font-bold">Ben Carter</p>
                                            <p class="text-xs opacity-50">ID: 2024-002</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span id="status-2" class="px-3 py-1 rounded-full bg-[#E8F5E9] text-[#0D3B2E] text-xs font-bold border border-[#0D3B2E]/10">Passing</span>
                                </td>
                                <td class="p-6 text-center">
                                    <input type="number" value="85" min="0" max="100" 
                                        class="grade-input w-16 text-lg font-bold text-[#0D3B2E]"
                                        oninput="updateGradeStatus(this, 'status-2')">
                                </td>
                                <td class="p-6">
                                    <input type="text" placeholder="Add comment..." class="w-full bg-transparent border-none text-sm opacity-60 focus:opacity-100 focus:outline-none placeholder-gray-500">
                                </td>
                            </tr>

                            <!-- Student Row 3 (Failing Example) -->
                            <tr class="hover:bg-white/40 transition group border-b border-white/20">
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=Cathy" class="w-8 h-8 rounded-full bg-white shadow-sm">
                                        <div>
                                            <p class="font-bold">Cathy Diaz</p>
                                            <p class="text-xs opacity-50">ID: 2024-003</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span id="status-3" class="px-3 py-1 rounded-full bg-[#EF5350]/20 text-[#EF5350] text-xs font-bold border border-[#EF5350]/20">Review</span>
                                </td>
                                <td class="p-6 text-center">
                                    <input type="number" value="58" min="0" max="100" 
                                        class="grade-input w-16 text-lg font-bold text-[#EF5350]"
                                        oninput="updateGradeStatus(this, 'status-3')">
                                </td>
                                <td class="p-6">
                                    <input type="text" value="Please see me after class." class="w-full bg-transparent border-none text-sm opacity-80 focus:opacity-100 focus:outline-none placeholder-gray-500 text-[#EF5350]">
                                </td>
                            </tr>

                            <!-- Student Row 4 (Empty) -->
                            <tr class="hover:bg-white/40 transition group border-b border-white/20">
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=Dave" class="w-8 h-8 rounded-full bg-white shadow-sm">
                                        <div>
                                            <p class="font-bold">Dave Evans</p>
                                            <p class="text-xs opacity-50">ID: 2024-004</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span id="status-4" class="px-3 py-1 rounded-full bg-white/50 text-[#0D3B2E]/50 text-xs font-bold border border-[#0D3B2E]/10">--</span>
                                </td>
                                <td class="p-6 text-center">
                                    <input type="number" placeholder="--" min="0" max="100" 
                                        class="grade-input w-16 text-lg font-bold text-[#0D3B2E]"
                                        oninput="updateGradeStatus(this, 'status-4')">
                                </td>
                                <td class="p-6">
                                    <input type="text" placeholder="Add comment..." class="w-full bg-transparent border-none text-sm opacity-60 focus:opacity-100 focus:outline-none placeholder-gray-500">
                                </td>
                            </tr>

                             <!-- Student Row 5 -->
                             <tr class="hover:bg-white/40 transition group border-b border-white/20">
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <img src="https://api.dicebear.com/9.x/avataaars/svg?seed=Elias" class="w-8 h-8 rounded-full bg-white shadow-sm">
                                        <div>
                                            <p class="font-bold">Elias Frank</p>
                                            <p class="text-xs opacity-50">ID: 2024-005</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-6">
                                    <span id="status-5" class="px-3 py-1 rounded-full bg-white/50 text-[#0D3B2E]/50 text-xs font-bold border border-[#0D3B2E]/10">--</span>
                                </td>
                                <td class="p-6 text-center">
                                    <input type="number" placeholder="--" min="0" max="100" 
                                        class="grade-input w-16 text-lg font-bold text-[#0D3B2E]"
                                        oninput="updateGradeStatus(this, 'status-5')">
                                </td>
                                <td class="p-6">
                                    <input type="text" placeholder="Add comment..." class="w-full bg-transparent border-none text-sm opacity-60 focus:opacity-100 focus:outline-none placeholder-gray-500">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer pagination -->
                <div class="p-4 border-t border-white/40 flex justify-end gap-2 text-sm opacity-60">
                    <span>Rows per page: 10</span>
                    <span class="mx-2">1-5 of 42</span>
                    <div class="flex gap-2">
                        <button class="hover:text-[#0D3B2E]"><i class="ph ph-caret-left"></i></button>
                        <button class="hover:text-[#0D3B2E]"><i class="ph ph-caret-right"></i></button>
                    </div>
                </div>

            </div>
        </div>

    </main>

    <!-- Success Toast (Hidden initially) -->
    <div id="toast" class="fixed bottom-10 right-10 glass px-6 py-4 rounded-xl transform translate-y-32 transition-transform duration-500 flex items-center gap-3 z-50">
        <div class="w-6 h-6 rounded-full bg-[#0D3B2E] flex items-center justify-center text-white">
            <i class="ph ph-check"></i>
        </div>
        <div>
            <h4 class="font-bold text-sm">Success</h4>
            <p class="text-xs opacity-70">Grades published successfully.</p>
        </div>
    </div>

    <script>
        // --- Navigation Logic ---
        function switchView(viewName) {
            const dashboard = document.getElementById('view-dashboard');
            const grading = document.getElementById('view-grading');
            const dashNav = document.getElementById('nav-dash');

            if (viewName === 'grading') {
                dashboard.classList.add('hidden-view');
                dashboard.classList.remove('active-view');
                
                // Small timeout to allow display:none to switch before transition
                setTimeout(() => {
                    dashboard.style.display = 'none';
                    grading.style.display = 'block';
                    // Trigger reflow
                    void grading.offsetWidth;
                    grading.classList.remove('hidden-view');
                    grading.classList.add('active-view');
                }, 300);
                
                dashNav.classList.remove('active');
            } else {
                grading.classList.add('hidden-view');
                grading.classList.remove('active-view');
                
                setTimeout(() => {
                    grading.style.display = 'none';
                    dashboard.style.display = 'grid'; // Grid for dashboard
                    void dashboard.offsetWidth;
                    dashboard.classList.remove('hidden-view');
                    dashboard.classList.add('active-view');
                }, 300);

                dashNav.classList.add('active');
            }
        }

        // --- Logic: Dynamic Grade Status ---
        function updateGradeStatus(input, statusId) {
            const score = parseInt(input.value);
            const statusBadge = document.getElementById(statusId);
            
            // Reset styles
            statusBadge.className = 'px-3 py-1 rounded-full text-xs font-bold border transition-all duration-300';
            input.classList.remove('text-[#EF5350]', 'text-[#0D3B2E]');

            if (isNaN(score)) {
                statusBadge.innerText = "--";
                statusBadge.classList.add('bg-white/50', 'text-[#0D3B2E]/50', 'border-[#0D3B2E]/10');
                input.classList.add('text-[#0D3B2E]');
                return;
            }

            if (score >= 60) {
                statusBadge.innerText = "Passing";
                statusBadge.classList.add('bg-[#E8F5E9]', 'text-[#0D3B2E]', 'border-[#0D3B2E]/10');
                input.classList.add('text-[#0D3B2E]');
            } else {
                statusBadge.innerText = "Review";
                // Red/Coral style for failure
                statusBadge.classList.add('bg-[#EF5350]/20', 'text-[#EF5350]', 'border-[#EF5350]/20');
                input.classList.add('text-[#EF5350]');
            }
        }

        // --- Logic: Mock Save ---
        function saveGrades() {
            const toast = document.getElementById('toast');
            toast.style.transform = 'translateY(0)';
            
            setTimeout(() => {
                toast.style.transform = 'translateY(150px)';
                switchView('dashboard');
            }, 2500);
        }
    </script>
</body>
</html>