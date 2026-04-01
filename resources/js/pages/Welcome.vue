<script setup lang="ts">
import { Head, Link }             from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted } from 'vue';
import { Button }                 from '@/components/ui/button';
import { Card, CardContent }      from '@/components/ui/card';
import { Separator }              from '@/components/ui/separator';
import { Badge }                  from '@/components/ui/badge';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';

interface Props { canRegister: boolean }
withDefaults(defineProps<Props>(), { canRegister: true });

const scrolled = ref(false);
const mx = ref(0); const my = ref(0);
const rx = ref(0); const ry = ref(0);
let raf = 0;

const stats = [
    { num: '80+',  label: 'Luxury rooms'     },
    { num: '5★',   label: 'Guest rating'     },
    { num: '24/7', label: 'Concierge'        },
    { num: '10+',  label: 'Years of service' },
];

const features = [
    'Private butler service for every suite',
    'Michelin-starred dining on premises',
    'Infinity pool overlooking the city skyline',
    'World-class spa and wellness centre',
    'Curated cultural experiences',
    'Exclusive members lounge access',
    'Seamless digital concierge system',
];

const rooms = [
    { name: 'Presidential Suite', price: '$850',   badge: 'Signature', span: true,  bg: 'from-orange-950 via-amber-900 to-orange-950'  },
    { name: 'Deluxe King',        price: '$320',   badge: null,        span: false, bg: 'from-slate-900 via-slate-800 to-slate-950'     },
    { name: 'Junior Suite',       price: '$480',   badge: 'Popular',   span: false, bg: 'from-yellow-950 via-amber-950 to-orange-900'   },
    { name: 'Penthouse',          price: '$1,200', badge: null,        span: false, bg: 'from-neutral-900 via-slate-900 to-neutral-950' },
    { name: 'Classic Double',     price: '$220',   badge: null,        span: false, bg: 'from-yellow-950 via-yellow-900 to-amber-950'   },
];

const testimonials = [
    { initials:'JM', name:'James Morrison',   origin:'London, UK',    text:'An absolutely transcendent experience. Haven is not merely a hotel — it is a philosophy of living. Attention to detail unlike anything I have encountered in years of travel.' },
    { initials:'SL', name:'Sophia Laurent',   origin:'Paris, France', text:'The Presidential Suite exceeded every expectation. The butler service was impeccable, and the view at sunrise was something I will carry with me forever.' },
    { initials:'AK', name:'Ahmed Al-Khalidi', origin:'Dubai, UAE',    text:'Every element was thoughtfully orchestrated. The staff remembered our preferences from a previous stay two years prior — the hallmark of true hospitality.' },
];

const navLinks: [string, string][]    = [['About','#about'],['Rooms','#rooms'],['Guests','#testimonials']];
const footerCols: { title: string; links: [string,string][] }[] = [
    { title:'Explore',        links:[['About Haven','#about'],['Our Rooms','#rooms'],['Guest Stories','#testimonials']] },
    { title:'Guest Services', links:[['Register','/client/register'],['Sign In','/client/login'],['Reservations','#']] },
    { title:'Contact',        links:[['Concierge','#'],['hello@haven.com','mailto:hello@haven.com'],['+1 (800) HAVEN','#']] },
];

function tick() {
    rx.value += (mx.value - rx.value) * 0.12;
    ry.value += (my.value - ry.value) * 0.12;
    raf = requestAnimationFrame(tick);
}

onMounted(() => {
    window.addEventListener('scroll', () => { scrolled.value = window.scrollY > 50; });
    document.addEventListener('mousemove', (e: MouseEvent) => { mx.value = e.clientX; my.value = e.clientY; });
    tick();

    const io = new IntersectionObserver(
        (en) => en.forEach((e) => { if (e.isIntersecting) e.target.classList.add('in-view'); }),
        { threshold: 0.12 },
    );
    document.querySelectorAll('.reveal').forEach((el) => io.observe(el));
});

onUnmounted(() => cancelAnimationFrame(raf));
</script>

<template>
    <Head title="Haven — Luxury Hotel" />

    <div class="dark">
    <div class="bg-background text-foreground overflow-x-hidden cursor-none min-h-screen">

        <!-- CURSOR -->
        <span class="fixed z-[9999] size-2 rounded-full bg-primary pointer-events-none -translate-x-1/2 -translate-y-1/2"
              :style="{ left: mx+'px', top: my+'px' }" />
        <span class="fixed z-[9998] size-8 rounded-full border border-primary/40 pointer-events-none -translate-x-1/2 -translate-y-1/2 transition-[width,height] duration-200"
              :style="{ left: rx+'px', top: ry+'px' }" />

        <!-- NAV -->
        <header :class="['fixed inset-x-0 top-0 z-50 transition-all duration-300',
                          scrolled ? 'bg-background/95 backdrop-blur-md border-b border-border'
                                   : 'bg-gradient-to-b from-background/80 to-transparent']">
            <div class="mx-auto max-w-screen-xl flex items-center justify-between px-8 py-5">
                <Link href="/" class="font-serif text-lg font-light tracking-[0.35em] text-primary uppercase no-underline">
                    Haven<span class="text-foreground/70"> Hotel</span>
                </Link>
                <nav class="hidden md:flex items-center gap-8">
                    <a v-for="[label,href] in navLinks" :key="label" :href="href"
                       class="text-[0.65rem] tracking-[0.2em] uppercase text-muted-foreground hover:text-primary transition-colors no-underline">
                        {{ label }}
                    </a>
                </nav>
                <div class="flex items-center gap-2">
                    <Button variant="ghost" size="sm"
                            class="text-[0.65rem] tracking-widest uppercase text-muted-foreground hover:text-primary hover:bg-primary/5 cursor-none rounded-none"
                            as-child>
                        <Link :href="$page.props.auth?.user ? '/dashboard' : '/client/login'">
                            {{ $page.props.auth?.user ? 'Dashboard' : 'Sign In' }}
                        </Link>
                    </Button>
                    <Button v-if="canRegister && !$page.props.auth?.user" size="sm"
                            class="text-[0.65rem] tracking-widest uppercase bg-primary text-primary-foreground hover:bg-primary/90 cursor-none rounded-none px-6"
                            as-child>
                        <Link href="/client/register">Book Now</Link>
                    </Button>
                </div>
            </div>
        </header>

        <!-- HERO -->
        <section class="relative h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0 grid gap-px z-0" style="grid-template-columns:1fr 2fr 1fr">
                <div v-for="(bg,i) in ['from-orange-950 via-amber-900 to-orange-950','from-slate-900 via-slate-800 to-slate-950','from-yellow-950 via-amber-950 to-orange-900']"
                     :key="i" class="relative overflow-hidden">
                    <div :class="`absolute inset-0 bg-gradient-to-br ${bg} hero-panel`" :style="`animation-delay:${i*-4}s`" />
                    <div class="absolute inset-0 bg-background/55" />
                </div>
            </div>
            <div class="absolute inset-0 z-[1] bg-gradient-to-br from-background/75 via-background/35 to-background/80" />
            <div class="absolute inset-0 z-[2] pointer-events-none overflow-hidden">
                <div class="absolute inset-y-0 left-1/2 w-px bg-gradient-to-b from-transparent via-primary/25 to-transparent animate-pulse" />
                <div class="absolute inset-x-0 top-[38%] h-px bg-gradient-to-r from-transparent via-primary/15 to-transparent animate-pulse [animation-delay:2s]" />
            </div>

            <div class="relative z-10 text-center px-6 hero-enter">
                <Badge variant="outline"
                       class="mb-8 border-primary/30 text-primary bg-primary/5 text-[0.6rem] tracking-[0.4em] uppercase rounded-none px-4 py-1.5">
                    Est. 2024 · Luxury Collection
                </Badge>
                <h1 class="font-serif font-light leading-[0.88] tracking-tight mb-4"
                    style="font-size:clamp(4.5rem,11vw,9.5rem)">
                    Haven
                    <em class="block text-primary" style="font-size:0.46em;letter-spacing:0.08em;font-style:italic">Luxury Hotel</em>
                </h1>
                <p class="text-[0.7rem] tracking-[0.35em] uppercase text-muted-foreground mb-12">
                    Where every moment becomes a memory
                </p>
                <div class="flex gap-4 justify-center flex-wrap">
                    <Button size="lg"
                            class="px-10 h-13 text-[0.65rem] tracking-widest uppercase bg-primary text-primary-foreground hover:bg-primary/90 cursor-none rounded-none"
                            as-child>
                        <Link href="/client/register">Reserve Your Stay</Link>
                    </Button>
                    <Button size="lg" variant="outline"
                            class="px-10 h-13 text-[0.65rem] tracking-widest uppercase border-foreground/20 hover:border-primary hover:text-primary cursor-none rounded-none"
                            as-child>
                        <a href="#rooms">Explore Rooms</a>
                    </Button>
                </div>
            </div>

            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2">
                <p class="text-[0.55rem] tracking-[0.4em] uppercase text-muted-foreground/40">Scroll</p>
                <div class="w-px h-14 bg-gradient-to-b from-primary/60 to-transparent animate-pulse" />
            </div>
        </section>

        <!-- ABOUT -->
        <section id="about" class="bg-card py-28 px-8">
            <div class="mx-auto max-w-screen-xl grid md:grid-cols-2 gap-16 items-center">
                <div class="reveal space-y-6">
                    <p class="text-[0.6rem] tracking-[0.5em] uppercase text-primary">Our Story</p>
                    <h2 class="font-serif font-light leading-tight" style="font-size:clamp(2.2rem,4.5vw,3.8rem)">
                        A Sanctuary of <em class="text-primary">Refined</em> Luxury
                    </h2>
                    <Separator class="w-14 bg-primary/60 h-px" />
                    <p class="text-sm leading-[1.9] text-muted-foreground">
                        Haven Luxury Hotel is more than a destination — an experience crafted for those who appreciate the art of fine living. Every detail reflects our unwavering commitment to excellence.
                    </p>
                    <p class="text-sm leading-[1.9] text-muted-foreground">
                        Nestled in timeless elegance, we offer a retreat where modern sophistication meets classical grace, creating moments that linger long after departure.
                    </p>
                    <div class="grid grid-cols-2 gap-3 pt-4 border-t border-border">
                        <Card v-for="s in stats" :key="s.num" class="bg-background/50 border-border/60 rounded-none">
                            <CardContent class="p-4">
                                <p class="font-serif font-light text-3xl text-primary leading-none mb-1">{{ s.num }}</p>
                                <p class="text-[0.6rem] tracking-widest uppercase text-muted-foreground/50">{{ s.label }}</p>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <div class="reveal">
                    <Card class="bg-background border-border/50 rounded-none relative overflow-hidden">
                        <div class="absolute top-0 left-10 right-10 h-px bg-gradient-to-r from-transparent via-primary to-transparent" />
                        <CardContent class="p-8">
                            <p class="font-serif font-light italic text-xl text-primary mb-6">The Haven Experience</p>
                            <ul class="divide-y divide-border/60">
                                <li v-for="f in features" :key="f"
                                    class="flex items-center gap-3 py-3 text-sm text-muted-foreground hover:text-foreground transition-colors group/item">
                                    <span class="size-1.5 rounded-full bg-primary/60 group-hover/item:bg-primary flex-shrink-0 transition-colors" />
                                    {{ f }}
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- ROOMS -->
        <section id="rooms" class="bg-background py-28 px-8">
            <div class="mx-auto max-w-screen-xl">
                <div class="reveal flex items-end justify-between mb-12">
                    <div>
                        <p class="text-[0.6rem] tracking-[0.5em] uppercase text-primary mb-3">Accommodations</p>
                        <h2 class="font-serif font-light" style="font-size:clamp(2.2rem,4.5vw,3.8rem)">
                            Curated <em class="text-primary">Spaces</em>
                        </h2>
                    </div>
                    <Button variant="outline"
                            class="text-[0.65rem] tracking-widest uppercase border-border hover:border-primary hover:text-primary cursor-none rounded-none"
                            as-child>
                        <Link href="/client/register">View All Rooms</Link>
                    </Button>
                </div>

                <div class="reveal grid gap-px"
                     style="grid-template-columns:2fr 1fr 1fr;grid-template-rows:290px 290px">
                    <div v-for="(room,i) in rooms" :key="room.name"
                         :class="['relative overflow-hidden cursor-none group', i===0 && 'row-span-2']">
                        <div :class="`absolute inset-0 bg-gradient-to-br ${room.bg} transition-transform duration-700 group-hover:scale-105`" />
                        <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-background/15 to-transparent flex flex-col justify-end p-6 transition-all duration-500 group-hover:from-background/95">
                            <p class="font-serif font-light text-xl mb-1">{{ room.name }}</p>
                            <p class="text-[0.65rem] tracking-widest uppercase text-primary">From {{ room.price }} / night</p>
                        </div>
                        <Badge v-if="room.badge"
                               class="absolute top-4 right-4 rounded-none bg-background/80 border border-primary/40 text-primary text-[0.58rem] tracking-widest uppercase px-3 py-1">
                            {{ room.badge }}
                        </Badge>
                    </div>
                </div>
            </div>
        </section>

        <!-- TESTIMONIALS -->
        <section id="testimonials" class="bg-card py-28 px-8">
            <div class="mx-auto max-w-screen-xl">
                <div class="reveal text-center mb-14">
                    <p class="text-[0.6rem] tracking-[0.5em] uppercase text-primary mb-4">Guest Voices</p>
                    <h2 class="font-serif font-light" style="font-size:clamp(2.2rem,4.5vw,3.8rem)">
                        Words of <em class="text-primary">Our Guests</em>
                    </h2>
                    <Separator class="w-10 bg-primary/60 h-px mx-auto mt-8" />
                </div>

                <div class="reveal grid md:grid-cols-3 gap-px">
                    <Card v-for="t in testimonials" :key="t.name"
                          class="bg-background border-border/50 rounded-none hover:border-primary/30 transition-colors">
                        <CardContent class="p-7 flex flex-col h-full">
                            <div class="flex gap-0.5 mb-5">
                                <span v-for="n in 5" :key="n" class="text-primary text-xs">★</span>
                            </div>
                            <p class="text-sm leading-[1.85] text-muted-foreground flex-1 mb-6">{{ t.text }}</p>
                            <Separator class="bg-border/60 mb-5" />
                            <div class="flex items-center gap-3">
                                <Avatar class="size-9 rounded-full border border-primary/25">
                                    <AvatarFallback class="bg-muted text-primary text-xs font-serif rounded-full">
                                        {{ t.initials }}
                                    </AvatarFallback>
                                </Avatar>
                                <div>
                                    <p class="text-xs font-medium tracking-wide text-foreground">{{ t.name }}</p>
                                    <p class="text-[0.6rem] tracking-widest uppercase text-muted-foreground/50">{{ t.origin }}</p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-background relative overflow-hidden py-36 px-8 text-center">
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="size-[700px] rounded-full bg-primary/[0.04] blur-[80px]" />
            </div>
            <div class="absolute inset-0 opacity-[0.02]"
                 style="background-image:linear-gradient(var(--primary) 1px,transparent 1px),linear-gradient(90deg,var(--primary) 1px,transparent 1px);background-size:60px 60px" />
            <div class="reveal relative z-10 mx-auto max-w-2xl space-y-7">
                <Badge variant="outline"
                       class="border-primary/30 text-primary bg-primary/5 text-[0.6rem] tracking-[0.4em] uppercase rounded-none px-4 py-1.5">
                    Begin Your Journey
                </Badge>
                <h2 class="font-serif font-light leading-tight" style="font-size:clamp(2.8rem,6.5vw,5.5rem)">
                    Reserve Your <em class="text-primary">Haven</em>
                </h2>
                <p class="text-[0.7rem] tracking-widest uppercase text-muted-foreground">
                    Join our community of distinguished guests
                </p>
                <div class="flex gap-4 justify-center flex-wrap pt-2">
                    <Button size="lg"
                            class="px-12 h-13 text-[0.65rem] tracking-widest uppercase bg-primary text-primary-foreground hover:bg-primary/90 cursor-none rounded-none"
                            as-child>
                        <Link href="/client/register">Create an Account</Link>
                    </Button>
                    <Button size="lg" variant="outline"
                            class="px-12 h-13 text-[0.65rem] tracking-widest uppercase border-border hover:border-primary hover:text-primary cursor-none rounded-none"
                            as-child>
                        <Link href="/client/login">Sign In</Link>
                    </Button>
                </div>
            </div>
        </section>

        <!-- FOOTER -->
        <footer class="bg-card border-t border-border px-8 pt-14 pb-8">
            <div class="mx-auto max-w-screen-xl">
                <div class="grid md:grid-cols-4 gap-10 mb-10">
                    <div>
                        <p class="font-serif font-light text-lg tracking-[0.35em] text-primary uppercase mb-4">Haven</p>
                        <p class="text-xs leading-loose text-muted-foreground/50 max-w-[200px]">
                            A sanctuary of refined luxury where timeless elegance meets modern sophistication.
                        </p>
                    </div>
                    <div v-for="col in footerCols" :key="col.title">
                        <p class="text-[0.58rem] tracking-[0.35em] uppercase text-primary mb-5">{{ col.title }}</p>
                        <ul class="space-y-3">
                            <li v-for="[label,href] in col.links" :key="label">
                                <a :href="href" class="text-xs text-muted-foreground/50 hover:text-primary transition-colors no-underline">
                                    {{ label }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <Separator class="bg-border/60 mb-6" />
                <div class="flex items-center justify-between text-[0.6rem] text-muted-foreground/40">
                    <p>© 2024 Haven Luxury Hotel. All rights reserved.</p>
                    <p>Crafted with care · <a href="#" class="text-primary/70 hover:text-primary transition-colors">Privacy Policy</a></p>
                </div>
            </div>
        </footer>

    </div>
    </div>
</template>

<style scoped>
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(36px); }
    to   { opacity: 1; transform: translateY(0);    }
}
@keyframes panelZoom {
    from { transform: scale(1);    }
    to   { transform: scale(1.06); }
}
.hero-enter { animation: fadeUp 1.1s ease forwards; }
.hero-panel { animation: panelZoom 14s ease-in-out infinite alternate; }
.reveal {
    opacity: 0;
    transform: translateY(28px);
    transition: opacity 0.85s ease, transform 0.85s ease;
}
.reveal.in-view { opacity: 1; transform: translateY(0); }
</style>