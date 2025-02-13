import ButtonLink from '@/Components/ButtonLink';
import Guest from '@/Layouts/GuestLayout';
import { PageProps } from '@/types';
import { Head } from '@inertiajs/react';

export default function Welcome({
    auth,
    laravelVersion,
    phpVersion,
}: PageProps<{ laravelVersion: string; phpVersion: string }>) {
    const handleImageError = () => {
        document
            .getElementById('screenshot-container')
            ?.classList.add('!hidden');
        document.getElementById('docs-card')?.classList.add('!row-span-1');
        document
            .getElementById('docs-card-content')
            ?.classList.add('!flex-row');
        document.getElementById('background')?.classList.add('!hidden');
    };

    return (
        <Guest>
            <Head title="Welcome" />
            <section className='h-full bg-white pt-16'>
                <div className="mx-auto px-10 md:pb-44 px-20 pt-32">
                    <div className="flex gap-x-20 gap-y-10 items-center flex-col lg:flex-row">
                        {/* banner text */}
                        <div className="flex-1">
                            <h6 className="ed-section-sub-title !text-black before:!content-none">
                                {'ONLINE '}
                                <span className="text-purple-500">
                                    Learning
                                </span>
                                {' COURSE'}
                            </h6>
                            <h1 className="font-medium text-[clamp(35px,5.4vw,80px)] text-blue-500 tracking-tight leading-[1.12] mb-[25px]">Explore Your Skills With <span className="font-bold"><span className="inline-block text-purple-500 relative before:absolute before:left-0 before:top-[calc(100%-6px)] before:w-[240px] before:h-[21px] before:bg-[url('../assets/img/banner-2-title-vector.svg')]">Online</span> Class</span></h1>
                            <p className="text-gray-500 font-medium mb-[41px]">Smply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry’s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled</p>
                            <div className="flex flex-wrap gap-[10px]">
                                <ButtonLink
                                    href={'/course'}
                                    className={'uppercase rounded-3xl'}
                                >
                                    start a course
                                </ButtonLink>
                                <ButtonLink
                                    href={'/course'}
                                    className={'uppercase rounded-3xl xs:w-full'}
                                >
                                    about us
                                </ButtonLink>
                                {/* <a href="about.html" className="ed-btn !bg-transparent border border-black !text-black hover:!bg-black hover:!text-white">about us</a> */}
                            </div>
                        </div>

                        {/* banner image */}
                        <div className="hidden md:flex flex-1">
                            <div className="relative z-[1] flex gap-[30px] items-center">
                                <img src={'https://picsum.photos/200/300'} alt="banner image" className="border-[10px] border-white rounded-[20px] max-w-[241px] aspect-[261/366]" />
                                <img src={'https://picsum.photos/200/300'} alt="banner image" className="rounded-[20px]" />
                                <div>
                                    <div className="w-[242px] aspect-square rounded-full bg-purple-500 opacity-80 blur-[110px] absolute -z-[1] bottom-0 left-[163px]"></div>
                                    <img src={'https://picsum.photos/200/300'} alt="vector" className="pointer-events-none absolute -z[1] top-[30px] -left-[35px]" />
                                    <img src={'https://picsum.photos/200/300'} alt="vector" className="pointer-events-none absolute -z[1] -top-[50px] -right-[40px]" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section className="h-full bg-white">
                <div className="mx-[9.2%] xxxl:mx-[8.2%] xxl:mx-[3%]">
                    {/* section heading */}
                    <div className="text-center mb-[21px]">
                        <h6 className="ed-section-sub-title">Our Courses</h6>
                        <h2 className="ed-section-title">Edutics Courses</h2>
                    </div>

                    <div className="ed-2-courses-filter-navs flex flex-wrap justify-center gap-[10px] mb-[40px] xs:mb-[30px] pb-[30px] xs:pb-[20px] border-b border-[#002147]/15 mx-[200px] lg:mx-[100px] md:mx-[12px] *:border *:border-purple-500 *:rounded-[6px] *:py-[5px] *:px-[10px] *:text-purple-500 *:font-medium *:text-[14px]">
                        <button className="hover:bg-purple-500 hover:text-white mixitup-control-active" data-filter="all">All</button>
                        <button className="hover:bg-purple-500 hover:text-white" data-filter=".personal-skill">Personal Skill</button>
                        <button className="hover:bg-purple-500 hover:text-white" data-filter=".web-dev">Web Development</button>
                        <button className="hover:bg-purple-500 hover:text-white" data-filter=".ui-ux-design">UX/UI Design</button>
                        <button className="hover:bg-purple-500 hover:text-white" data-filter=".data-science">Data Science</button>
                        <button className="hover:bg-purple-500 hover:text-white" data-filter=".finance">Finance</button>
                    </div>

                    {/* course cards */}


                    <div className="max-w-sm bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 p-5">
                        <a href="#">
                            <img className="rounded-t-lg" src={'https://flowbite.com/docs/images/blog/image-1.jpg'} alt="" />
                        </a>
                        <div className='py-5'>
                            <div className='flex'>
                                <div className='px-2 py-1 border border-gray-500'>
                                    {'Expert'}
                                </div>
                            </div>
                            <p className="mb-3 font-normal text-gray-700 dark:text-gray-400">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
                            <a href="#" className="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Read more
                                <svg className="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9" />
                                </svg>
                            </a>
                        </div>
                    </div>

                </div>
            </section>
        </Guest>
    );
}
