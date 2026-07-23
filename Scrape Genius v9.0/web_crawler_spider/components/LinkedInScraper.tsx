"use client";

import { useState, useRef } from "react";
import { useRouter } from "next/navigation";

interface LinkedInProfileResult {
  firstName?: string;
  lastName?: string;
  headline?: string;
  location?: string;
  industry?: string;
  summary?: string;
  experience?: Array<{
    title: string;
    company: string;
    duration: string;
    description: string;
  }>;
  education?: Array<{
    school: string;
    degree: string;
    fieldOfStudy: string;
    duration: string;
  }>;
  skills?: string[];
  contactInfo?: {
    email?: string;
    phone?: string;
    website?: string;
    linkedInUrl?: string;
  };
}

export default function LinkedInScraper() {
  const [profileUrl, setProfileUrl] = useState("");
  const [sessionCookie, setSessionCookie] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [result, setResult] = useState<LinkedInProfileResult | null>(null);
  const [error, setError] = useState<string | null>(null);
  const router = useRouter();
  const abortControllerRef = useRef<AbortController | null>(null);

  const handleScrape = async () => {
    if (!profileUrl || !sessionCookie) {
      setError("Please enter both LinkedIn profile URL and session cookie");
      return;
    }

    setIsLoading(true);
    setError(null);
    setResult(null);
    
    abortControllerRef.current = new AbortController();

    try {
      // In a real implementation, this would call our API endpoint
      // that uses the @n-h-n/linkedin-profile-scraper package on the server side
      const response = await fetch("/api/social/linkedin", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          profileUrl,
          sessionCookieValue: sessionCookie,
        }),
        signal: abortControllerRef.current.signal,
      });

      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.error || "Failed to scrape LinkedIn profile");
      }

      const data = await response.json();
      setResult(data.result);
    } catch (err: any) {
      if (err.name === "AbortError") {
        console.log("Request was aborted");
      } else {
        setError(err.message || "An error occurred while scraping the profile");
        console.error("LinkedIn scraping error:", err);
      }
    } finally {
      setIsLoading(false);
    }
  };

  const handleAbort = () => {
    if (abortControllerRef.current) {
      abortControllerRef.current.abort();
      setIsLoading(false);
    }
  };

  const handleExport = () => {
    if (!result) return;
    
    const dataStr = JSON.stringify(result, null, 2);
    const dataUri = `data:application/json;charset=utf-8,${encodeURIComponent(dataStr)}`;
    
    const exportFileDefaultName = `linkedin-profile-${Date.now()}.json`;
    
    const linkElement = document.createElement('a');
    linkElement.setAttribute('href', dataUri);
    linkElement.setAttribute('download', exportFileDefaultName);
    linkElement.click();
  };

  return (
    <div className="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
      <h2 className="text-2xl font-bold mb-6 text-gray-900 dark:text-white">LinkedIn Profile Scraper</h2>
      
      <div className="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
        <h3 className="font-semibold text-yellow-800 dark:text-yellow-200 flex items-center">
          ⚠️ Important Notice
        </h3>
        <p className="mt-2 text-yellow-700 dark:text-yellow-300 text-sm">
          Scraping LinkedIn profiles violates their Terms of Service and may result in account suspension or IP blocking. 
          Use this feature responsibly and consider using LinkedIn's official APIs for compliance. Always respect robots.txt 
          and rate limits. We are not responsible for any consequences of using this tool.
        </p>
      </div>

      <div className="space-y-4">
        <div>
          <label htmlFor="profileUrl" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            LinkedIn Profile URL
          </label>
          <input
            id="profileUrl"
            type="url"
            value={profileUrl}
            onChange={(e) => setProfileUrl(e.target.value)}
            placeholder="https://www.linkedin.com/in/username"
            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            disabled={isLoading}
          />
          <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Enter the full URL of the LinkedIn profile you want to scrape
          </p>
        </div>

        <div>
          <label htmlFor="sessionCookie" className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            LinkedIn Session Cookie (li_at)
          </label>
          <input
            id="sessionCookie"
            type="password"
            value={sessionCookie}
            onChange={(e) => setSessionCookie(e.target.value)}
            placeholder="Enter your li_at cookie value"
            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
            disabled={isLoading}
          />
          <p className="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Your LinkedIn session cookie (li_at) is required to access profile data
          </p>
        </div>

        <div className="flex space-x-3 pt-2">
          <button
            onClick={handleScrape}
            disabled={isLoading}
            className="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {isLoading ? "Scraping..." : "Scrape Profile"}
          </button>
          
          {isLoading && (
            <button
              onClick={handleAbort}
              className="px-4 py-2 bg-red-600 text-white font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            >
              Abort
            </button>
          )}
          
          {result && (
            <button
              onClick={handleExport}
              className="px-4 py-2 bg-green-600 text-white font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Export Data
            </button>
          )}
        </div>

        {error && (
          <div className="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <p className="text-red-800 dark:text-red-200">{error}</p>
          </div>
        )}

        {result && (
          <div className="mt-6 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
            <div className="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
              <h3 className="text-lg font-medium text-gray-900 dark:text-white">Scraped Profile Data</h3>
            </div>
            <div className="p-4 max-h-96 overflow-y-auto">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {result.firstName || result.lastName ? (
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white">Name</h4>
                    <p className="text-gray-700 dark:text-gray-300">
                      {result.firstName} {result.lastName}
                    </p>
                  </div>
                ) : null}
                
                {result.headline ? (
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white">Headline</h4>
                    <p className="text-gray-700 dark:text-gray-300">{result.headline}</p>
                  </div>
                ) : null}
                
                {result.location ? (
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white">Location</h4>
                    <p className="text-gray-700 dark:text-gray-300">{result.location}</p>
                  </div>
                ) : null}
                
                {result.industry ? (
                  <div>
                    <h4 className="font-medium text-gray-900 dark:text-white">Industry</h4>
                    <p className="text-gray-700 dark:text-gray-300">{result.industry}</p>
                  </div>
                ) : null}
              </div>
              
              {result.summary ? (
                <div className="mt-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">Summary</h4>
                  <p className="text-gray-700 dark:text-gray-300 mt-1">{result.summary}</p>
                </div>
              ) : null}
              
              {result.experience && result.experience.length > 0 ? (
                <div className="mt-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">Experience</h4>
                  <ul className="mt-2 space-y-2">
                    {result.experience.map((exp, index) => (
                      <li key={index} className="border-l-4 border-blue-500 pl-2 py-1">
                        <div className="font-medium text-gray-900 dark:text-white">{exp.title}</div>
                        <div className="text-gray-700 dark:text-gray-300">{exp.company}</div>
                        <div className="text-sm text-gray-500 dark:text-gray-400">{exp.duration}</div>
                        {exp.description && (
                          <div className="text-sm text-gray-600 dark:text-gray-400 mt-1">{exp.description}</div>
                        )}
                      </li>
                    ))}
                  </ul>
                </div>
              ) : null}
              
              {result.education && result.education.length > 0 ? (
                <div className="mt-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">Education</h4>
                  <ul className="mt-2 space-y-2">
                    {result.education.map((edu, index) => (
                      <li key={index} className="border-l-4 border-green-500 pl-2 py-1">
                        <div className="font-medium text-gray-900 dark:text-white">{edu.school}</div>
                        {edu.degree && (
                          <div className="text-gray-700 dark:text-gray-300">{edu.degree}{edu.fieldOfStudy ? ` in ${edu.fieldOfStudy}` : ''}</div>
                        )}
                        <div className="text-sm text-gray-500 dark:text-gray-400">{edu.duration}</div>
                      </li>
                    ))}
                  </ul>
                </div>
              ) : null}
              
              {result.skills && result.skills.length > 0 ? (
                <div className="mt-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">Skills</h4>
                  <div className="mt-2 flex flex-wrap gap-2">
                    {result.skills.map((skill, index) => (
                      <span 
                        key={index} 
                        className="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-200 dark:text-blue-900"
                      >
                        {skill}
                      </span>
                    ))}
                  </div>
                </div>
              ) : null}
              
              {result.contactInfo && (
                <div className="mt-4">
                  <h4 className="font-medium text-gray-900 dark:text-white">Contact Info</h4>
                  <div className="mt-2 grid grid-cols-1 md:grid-cols-2 gap-2">
                    {result.contactInfo.email && (
                      <div>
                        <span className="text-gray-500 dark:text-gray-400">Email: </span>
                        <span className="text-gray-900 dark:text-white">{result.contactInfo.email}</span>
                      </div>
                    )}
                    {result.contactInfo.phone && (
                      <div>
                        <span className="text-gray-500 dark:text-gray-400">Phone: </span>
                        <span className="text-gray-900 dark:text-white">{result.contactInfo.phone}</span>
                      </div>
                    )}
                    {result.contactInfo.website && (
                      <div>
                        <span className="text-gray-500 dark:text-gray-400">Website: </span>
                        <a 
                          href={result.contactInfo.website} 
                          target="_blank" 
                          rel="noopener noreferrer"
                          className="text-blue-600 hover:underline dark:text-blue-400"
                        >
                          {result.contactInfo.website}
                        </a>
                      </div>
                    )}
                    {result.contactInfo.linkedInUrl && (
                      <div>
                        <span className="text-gray-500 dark:text-gray-400">LinkedIn: </span>
                        <a 
                          href={result.contactInfo.linkedInUrl} 
                          target="_blank" 
                          rel="noopener noreferrer"
                          className="text-blue-600 hover:underline dark:text-blue-400"
                        >
                          View Profile
                        </a>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}